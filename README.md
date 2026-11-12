# 应用广告拦截

GKD 安卓广告拦截订阅，毫秒级响应，精准不误触。

## 订阅地址
```
https://webcat.bbroot.com/GKD-subscription.json3
```

## 当前版本
- 版本号：407
- 订阅 ID：777
- 应用：886 个
- 全局规则组：开屏广告、更新提示、青少年模式、启动所有权限禁止请求

## 功能
- 开屏广告：自动跳过开屏广告
- 更新提示：关闭应用更新弹窗
- 青少年模式：关闭青少年模式弹窗
- 启动所有权限禁止请求：自动拒绝权限请求弹窗（默认关闭，需手动开启）
- 其余 7 类规则（评价提示、通知提示、权限提示、局部广告、全屏广告、分段广告、功能类、其他）默认关闭，按需在 GKD 内开启

## 分类
开屏广告 / 青少年模式 / 更新提示 / 评价提示 / 通知提示 / 权限提示 / 局部广告 / 全屏广告 / 分段广告 / 功能类 / 其他

## 贡献
欢迎提交规则补充和修复，查看 [贡献指南](https://github.com/webcstx/kd-rules-update/blob/main/CONTRIBUTING.md)。


## 自由签发 SSL 证书
访问 [https://webcat.bbroot.com/分发证书/](https://webcat.bbroot.com/%E5%88%86%E5%8F%91%E8%AF%81%E4%B9%A6/) ，填写域名与有效时长，验证域名归属后即可签发。

站点自建了一张根证书（自由签发 CA，组织字段 O=自由签发），装进设备信任列表后，用它签发的证书即被系统信任，有效期可自行设定。

- 有效时长支持天、个月、年三种单位，任意填写，签发完成后直接显示生效与失效时间
- 签发前需在 DNS 添加 TXT 记录 `_webcat-verify.你的域名`，验证通过才会签发
- 证书与私钥全部在浏览器本地生成，不上传服务器
- 支持用口令导出加密的 PKCS#12 容器（`.p12`），内含证书与私钥，适合加密传输
- 签发的是自签证书，浏览器不会信任，仅适合内网、测试与内部系统；公网免警告必须使用公共 CA 证书

## Star History
<a href="https://star-history.com/#webcstx/kd-rules-update&Date">
 <picture>
   <source media="(prefers-color-scheme: dark)" srcset="https://api.star-history.com/svg?repos=webcstx/kd-rules-update&type=Date&theme=dark" />
   <source media="(prefers-color-scheme: light)" srcset="https://api.star-history.com/svg?repos=webcstx/kd-rules-update&type=Date" />
   <img alt="Star History Chart" src="https://api.star-history.com/svg?repos=webcstx/kd-rules-update&type=Date" />
 </picture>
</a>

## 作者
webcatx
