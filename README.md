# 自由签发 SSL 证书分发

为你的域名签发 SSL 证书，有效期可自定义，证书与私钥全程在浏览器本地生成。

在线地址：https://webcat.bbroot.com/

## 功能

- 填写域名，自动写入证书的 SAN 扩展，支持多个域名、通配符与 IP
- 有效时长有一个单独的输入框，填数字后点选「天 / 月 / 年」，输入过程实时回显当前设置与预计到期时间，签发完成显示准确失效时间
- 直接签发：不校验域名归属，也不查询任何 DNS 记录，填域名即可出证
- 支持用口令导出加密的 PKCS#12 容器（`.p12`），内含证书与私钥，适合加密传输
- 证书与私钥不上传任何服务器，关闭页面即清除

## 信任机制

站点自建一张根证书（自由签发 CA：`CN=https://webcat.bbroot.com/分发证书, O=自由签发`，有效期 20 年），由其签发站点证书。

- 把 `rootCA.crt` 装进设备信任列表后，本站签发的证书即被系统信任，有效期不受公共 CA 限制
- 安卓：设置 → 安全 → 加密与凭据 → 安装证书 → CA 证书
- Windows：双击导入，证书存储选「受信任的根证书颁发机构」
- iOS：用描述文件安装，再到「关于本机 → 证书信任设置」开启完全信任

注意：自建根证书只对装过它的设备生效；没装过的设备仍会提示证书不受信任。要让所有人默认信任，只能使用公共 CA 签发的证书，而公共 CA 的有效期不可自定义。

## 在服务器上启用 HTTPS

签发成功后，页面结果区会直接给出 Nginx 与 Apache 两段配置，域名自动替换成你签发时填的域名，可一键复制。

先上传证书（示例路径 `/etc/ssl/webcat/`）：

```
cert.pem  → /etc/ssl/webcat/cert.pem
key.pem   → /etc/ssl/webcat/key.pem
```

Nginx 关键配置：

```
server {
    listen 443 ssl;
    server_name 你的域名;

    ssl_certificate     /etc/ssl/webcat/cert.pem;
    ssl_certificate_key /etc/ssl/webcat/key.pem;
    ssl_protocols       TLSv1.2 TLSv1.3;

    location / {
        root  /var/www/html;
        index index.html;
    }
}

server {
    listen 80;
    server_name 你的域名;
    return 301 https://$host$request_uri;
}
```

Apache 关键配置：

```
<VirtualHost *:443>
  ServerName 你的域名
  SSLEngine on
  SSLCertificateFile    /etc/ssl/webcat/cert.pem
  SSLCertificateKeyFile /etc/ssl/webcat/key.pem
  DocumentRoot /var/www/html
</VirtualHost>
```

改完先自检再重载：

```
nginx -t && nginx -s reload
apachectl configtest && systemctl reload httpd
```

前提仍然是客户端装过 `rootCA.crt`，否则浏览器会提示证书不受信任。

## 关于 CAA

CAA（RFC 8659）是 DNS 里声明“哪些公共 CA 可以给本域名签证书”的记录，只有公共 CA 在签发前会查询它：

- 它与本站的自建 CA 无关，本站签发不检查 CAA，也不会被 CAA 拦截
- 它不能替代根证书安装。证书是否被信任，只取决于客户端有没有装 `rootCA.crt`
- `0 issue ";"` 的含义是“不允许任何 CA 签发”，不是“宽松放行”
- `issue` 的值只能是 CA 域名，写成网址或路径属于非标准用法

如果域名同时用 GitHub Pages 托管，证书由 Let's Encrypt 签发，配置 CAA 时必须保留 `letsencrypt.org`，否则到期续签会被拒绝。

## 使用方式

1. 打开站点，填写域名；在有效时长输入框里输入数字，再点「天」或「月」或「年」，下方会实时显示当前设置与预计到期时间
2. 点「签发证书」
3. 页面立即生成证书与私钥，并显示失效时间
4. 签发成功后下载 `cert.pem` 与 `key.pem`，或用口令导出加密的 `.p12`
5. 结果区还会给出 Nginx / Apache 的 HTTPS 配置，复制到服务器重载即可开启 HTTPS

## 许可证

本项目采用 [Apache License 2.0](LICENSE)。

## 作者

webcatx
