opauth-qqweibo
=========
Opauth strategy for QQ Weibo authentication.

Getting started
----------------
0. Make sure your website installation supports UTF-8

1. Install Opauth-QQWeibo:
   ```bash
   cd path_to_opauth/Strategy
   git clone https://github.com/lixiphpdotcom/opauth-qqweibo.git QQWeibo
   ```
2. Create QQ Weibo application at http://open.t.qq.com/
	 - It is a web application
	 - Callback: http://path_to_opauth/qqweibo_callback

3. Configure Opauth-QQWeibo strategy with `key` and `secret`.

4. Direct user to `http://path_to_opauth/qqweibo` to authenticate

Strategy configuration
----------------------

Required parameters:

```php
<?php
'QQWeibo' => array(
	'key' => 'YOUR APP KEY',
	'secret' => 'YOUR APP SECRET'
)
```

License
---------
Opauth-QQWeibo is MIT Licensed  
