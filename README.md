# README #

Class for executing calls to WHM API, using cPanel API2 functions for the calls

### What is this repository for? ###

* Just-do-the-job class for executing calls to WHM API, using cPanel API2 functions for the calls
* Version 0.0.1

### How do I get set up? ###


```
#!php

require 'class.whm_api.php';
```

### Usage ###


```
#!php
$host = '1.0.0.127'; // WHM IP address
$whm_username = 'root'; // WHM login username, usually 'root' with full access permissions
$hash = 'hash_or_pass'; // WHM password or a hash string from WHM >> Clusters >> Remote Access Key
$cpanel_username = 'username'; // cPanel login username, enter if using cPanel API2 functions
// initialize
$whm_api = new WHM_API($host, $whm_username, $hash, $cpanel_username);

// make a call
$module = 'MysqlFE';
$function = 'listdbs';
$params = array(
	'domain' => 'example.com',
	);
$test = $whm_api->cpanel_api2( $module, $function, $params);
```


### Who do I talk to? ###

* Repo owner or admin