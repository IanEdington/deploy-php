<?php

require_once(dirname(__FILE__) . "/functions.php");

// Load yaml/json into deploy-config object.
$json = file_get_contents("config.json");
$config_file = json_decode($json, true);
$settings = array();
$repository = '@defalt-repo@';
$branch = '@default-branch@';

$settings = cascading_settings($config_file, $settings, $repository, $branch);
$default = $settings['@default-repo@']['@default-branch@'];

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
    based on the repo and branch determin which deployments
    will be triggered (possibly more than one. but start with one)

Once the options are built run through each deployment script echoing output along the way.
EOT;
