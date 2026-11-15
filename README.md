# Free Issuance SSL 证书分发
本地浏览器自签证书工具，全程在前端完成生成，不会上传域名、不会发起DNS查询、无CAA校验。

## 证书信息
- 根证书：CN=Free Issuance Root CA, O=Free Issuance
- 站点证书：CN=Free Issuance, O=Free Issuance
- 域名存放位置：仅写入SAN扩展，不放在通用名称CN字段
- 根证书规则：首次打开页面生成，浏览器本地存储复用，页面无重置按钮，如需重新生成，清除站点localStorage

## 使用步骤
1. 域名输入框填写目标域名，支持单域名、通配符域名
2. 使用日期选择器，直接选定证书到期日期，不按天数自动推算
3. 点击【生成证书】
4. 下载文件：rootCA.crt、cert.pem、fullchain.pem、私钥key.pem

## Star History

<a href="https://www.star-history.com/?repos=webcstx%2Fdoubao-quick-tools&type=date&legend=top-left">
 <picture>
   <source media="(prefers-color-scheme: dark)" srcset="https://api.star-history.com/chart?repos=webcstx/doubao-quick-tools&type=date&theme=dark&legend=top-left" />
   <source media="(prefers-color-scheme: light)" srcset="https://api.star-history.com/chart?repos=webcstx/doubao-quick-tools&type=date&legend=top-left" />
   <img alt="Star History Chart" src="https://api.star-history.com/chart?repos=webcstx/doubao-quick-tools&type=date&legend=top-left" />
 </picture>
</a>
