<?php

function find_branch_and_repo ($arr, $repository, $branch)
{
	if ( array_key_exists('repository', $arr) ) {
		$repository = $arr['repository'];
	}
	if ( array_key_exists('branch', $arr) ) {
		$branch = $arr['branch'];
		echo 'this is where the branch is set'.PHP_EOL;
		echo $branch.PHP_EOL;
	}
	return array($repository, $branch);
}

function cascade_into_next_level ($arr, $settings, $repository, $branch)
{
	$check_arr = ["repository", "branch", "target_dir"];

	if ( is_array($arr)) {
		foreach ($arr as $key => $value) {
			if (  !in_array($key, $check_arr) ){
				echo 'cascade this:'.PHP_EOL;
				echo print_r($value).PHP_EOL;
				$settings = cascading_settings ($settings, $value, $repository, $branch);
			}
		}
	}
	return $settings;
}

function structure_settings_by_branch ($settings, $repository, $branch)
{
	if ( array_key_exists( $repository, $settings) ) {
		if ( array_key_exists( $branch, $settings[$repository] )) {
		} else {
			$settings[$repository][$branch] = array();
		}
	} else {
		$settings[$repository] = array(
			$branch => array()
		);
	}
	return $settings;
}

function cascading_settings ($arr, $settings, $repository, $branch)
{
	list ($repository, $branch) = find_branch_and_repo($arr, $repository, $branch);
	echo $repository.PHP_EOL;
	echo $branch.PHP_EOL;

	$settings = cascade_into_next_level($arr, $settings, $repository, $branch);

	$settings = structure_settings_by_branch ($settings, $repository, $branch);

	$check_arr = ['repository', 'branch', 'target_dir'];
	foreach ($arr as $key => $value) {
		if ( in_array( $key, $check_arr ) ){
			$settings[$repository][$branch][$key] = $value;
		}
	}
	return $settings;
}

$json = file_get_contents("config.json");
$arr = json_decode($json, true);
$settings = array();
$repository = 'fallback';
$branch = 'fallback';

$settings = cascading_settings ($arr, $settings, $repository, $branch);

echo print_r($settings);


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

