<?php
GLOBAL $config;

require("../grplib-php/router.php");
require("config.php");

if($config['maintenanceMode'] == true)
{
	require("API/error/maintenance.php");
}
else if ($config['serviceClosed'] == true)
{
	require("API/error/service_closed.php");
}
else
{
	route('GET', '/', function ()
		{
			GLOBAL $config;
			print 'This is The Discovery API Endpoint!<br>';
			print '<br>';
		}
	);
	
	route('GET', '/v1/endpoint', function ()
		{
			require("API/endpoint.php");
		}
	);
}

run_router(); // Start Router