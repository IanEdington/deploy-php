<?php

class CheckStructure
{
	public function repository( $key, $val )
	{
		return true;
	}
	public function branch( $key, $val )
	{
		return true;
	}
	public function target_dir( $key, $val )
	{
		return true;
	}
	public function path_to_git( $key, $val )
	{
		return true;
	}
	public function path_to_rsync( $key, $val )
	{
		return true;
	}
	public function path_to_composer( $key, $val )
	{
		return true;
	}
	public function composer( $key, $val )
	{
		return true;
	}
	public function composer_options( $key, $val )
	{
		return true;
	}
	public function email_on_error( $key, $val )
	{
		return true;
	}
	public function exclude( $key, $val )
	{
		return true;
	}
	public function delete_files( $key, $val )
	{
		return true;
	}
}


function cascading_settings ($settings, $arr, $repository, $branch){
	global $check;
	if ( is_array($arr)){
	if ( array_key_exists('repository', $arr) ) {
		$check->repository(1,1);
		$repository = $arr['repository'];
	}
	if ( array_key_exists('branch', $arr) ) {
		$check->branch(1,1);
		$branch = $arr['branch'];
	}

	foreach ($arr as $key => $value) {
		if ( !method_exists($check, $key) ){
			$settings = cascading_settings ($settings, $value, $repository, $branch);
		}
	}

	if ( array_key_exists( $repository, $settings) ) {
		if ( !array_key_exists( 'branch', $settings->{$repository} )) {
			$settings[$repository][$branch] = array();
		}
	} else {
		$settings[$repository] = array(
			$branch => array()
		);
	}

		foreach ($arr as $key => $value) {
			if ( !method_exists($check, $key) ){
				$settings[$repository][$branch][$key] = $value;
			}
		}
	}
	return $settings;
}

$check = new CheckStructure();
$json = file_get_contents("deployment-config.json");
$arr = json_decode($json, true);
$settings = array();
$repository = 'null';
$branch = 'null';

$settings = cascading_settings ($arr, $settings, $repository, $branch);

echo print_r($settings) . PHP_EOL;
echo 'hello';


$hello = <<<EOT

MAKE SURE It's Secure
	Load yaml/json into deploy-config object.

	Check payload hash against all SECRET\_ACCESS\_TOKEN's to findout if matches one of them.
	if it matches load the payload
	if it doesn't throw and error

Does the server have everything it needs?

MAKE SURE IT'S SETUP properly
	get the repo and branch from the payload
		"ref": "refs/heads/staging",
		"repository": {
			"full_name": "EyecarePD/eyecarepd"
		}
	check if repo and branch match a deployment config
		yes - keep going
		no - throw an error

Build up the options
	based on the repo and branch determin which deployments will be triggered (possibly more than one. but start with one)

Once the options are built run through each deployment script echoing output along the way.
EOT;

