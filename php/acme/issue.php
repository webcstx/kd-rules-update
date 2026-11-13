<?php
/**
 * 命令行签发脚本（本仓库编写，非第三方代码）
 * 依赖同目录下的 ACMECert（MIT，作者 Stefan Körfgen）走 ACME v2 / DNS-01。
 *
 * 用法：
 *   php issue.php --domain example.com --email you@example.com
 *   php issue.php --domain example.com --domain *.example.com --ca staging
 *
 * 说明：
 *   1. 首次运行会用邮箱注册 ACME 账号，账号密钥保存在 account_key.pem
 *   2. 每次验证会打印需要添加的 TXT 记录，添加完按回车继续
 *   3. 通配符证书只能用 dns-01
 */

require __DIR__ . '/ACMECert.php';

$opts = getopt('', ['domain:', 'email::', 'ca::', 'out::', 'account::']);

$domains = isset($opts['domain']) ? (array)$opts['domain'] : [];
if (!$domains) {
    fwrite(STDERR, "用法：php issue.php --domain example.com [--domain *.example.com] [--email you@example.com] [--ca staging] [--out /path]\n");
    exit(1);
}

$ca = isset($opts['ca']) && $opts['ca'] === 'staging'
    ? 'https://acme-staging-v02.api.letsencrypt.org/directory'
    : 'https://acme-v02.api.letsencrypt.org/directory';

$outDir     = isset($opts['out']) ? rtrim($opts['out'], '/') : __DIR__;
$accountKey = isset($opts['account']) ? $opts['account'] : $outDir . '/account_key.pem';
$certKey    = $outDir . '/cert_key.pem';

if (!is_dir($outDir)) {
    mkdir($outDir, 0700, true);
}

$ac = new ACMECert($ca);

if (file_exists($accountKey)) {
    $ac->loadAccountKey('file://' . $accountKey);
    echo "使用已有账号密钥：{$accountKey}\n";
} else {
    $email = isset($opts['email']) ? $opts['email'] : '';
    if (!$email) {
        fwrite(STDERR, "首次运行需要 --email 注册 ACME 账号（用于接收到期提醒）。\n");
        exit(1);
    }
    $pem = $ac->generateRSAKey(2048);
    file_put_contents($accountKey, $pem);
    chmod($accountKey, 0600);
    $ac->loadAccountKey($pem);
    $ac->register(true, ['mailto:' . $email]);
    echo "已注册 ACME 账号，密钥写入：{$accountKey}\n";
}

$pem = file_exists($certKey) ? file_get_contents($certKey) : $ac->generateRSAKey(2048);
file_put_contents($certKey, $pem);
chmod($certKey, 0600);

$domainConfig = [];
foreach ($domains as $d) {
    $domainConfig[$d] = ['challenge' => 'dns-01'];
}

$handler = function ($params) {
    echo "\n请添加 DNS 记录：\n";
    echo "  主机记录：{$params['key']}\n";
    echo "  记录类型：TXT\n";
    echo "  记录值　：{$params['value']}\n";
    echo "添加并等待解析生效后按回车继续…";
    fgets(STDIN);
    return function ($params) {
        echo "验证完成，可以删除 {$params['key']} 的 TXT 记录。\n";
    };
};

try {
    $chain = $ac->getCertificateChain($pem, $domainConfig, $handler);
} catch (Exception $e) {
    fwrite(STDERR, "签发失败：" . $e->getMessage() . "\n");
    exit(1);
}

file_put_contents($outDir . '/fullchain.pem', $chain);
chmod($outDir . '/fullchain.pem', 0600);
file_put_contents($outDir . '/privkey.pem', $pem);

echo "\n签发成功：\n";
echo "  证书链：{$outDir}/fullchain.pem\n";
echo "  私钥　：{$outDir}/privkey.pem\n";

$days = $ac->getRemainingDays($chain);
if ($days !== false) {
    echo "  剩余有效期：{$days} 天\n";
}
