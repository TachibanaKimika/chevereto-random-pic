# chevereto-random-pic

本地chevereto图床的随机图api, 将本文件部署到你的web服务器中即可直接使用
调用示例:`http://yourphpURL?ablum=ウマ娘&return=https`

- 需要将图片的储存格式设置成按照日期储存
- php需要安装mysqli模块, 并能连接到cheverto的mysql数据库
- 记得修改`$con = new mysqli("127.0.0.1","root","password","chevereto","3307")`中的各个参数
- 记得修改`$img_url="yourcheveretoURL/images/"`中`yourcheveretoURL`为你图床的域名
