<?php

function find_branch_and_repo($configPart, $repository, $branch)
{
    if (array_key_exists('repository', $configPart)) {
        $repository = $configPart['repository'];
    }
    if (array_key_exists('branch', $configPart)) {
        $branch = $configPart['branch'];
    }
    return array($repository, $branch);
}

function cascade_into_next_level($configPart, $settings, $repository, $branch)
{
    $validOptions = ["exclude", "extra-commands"];

    foreach ($configPart as $key => $value) {
        if (!in_array($key, $validOptions)) { // check.cascade($key)
            $settings = cascading_settings($value, $settings, $repository, $branch);
        }
    }
    return $settings;
}

function structure_settings_by_branch($settings, $repository, $branch)
{
    if (array_key_exists($repository, $settings)) {
        if (array_key_exists($branch, $settings[$repository])) {
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

function cascading_settings($configPart, $settings, $repository, $branch)
{
    if (is_array($configPart)) {
        list ($repository, $branch) = find_branch_and_repo($configPart, $repository, $branch);
    }

    $settings = structure_settings_by_branch($settings, $repository, $branch);

    if (is_array($configPart)) {
        $validOptions = [
            "target_dir",
            "email-on-error",
            "git",
            "rsync",
            "exclude",
            "extra-commands",
            "repository",
            "staging_dir",
            "secret_access_token",
            "clean_up",
            "command_time_limit",
            "backup_dir",
            "composer",
            "composer-optoins"
        ];

        if (is_array($configPart)) {
            foreach ($configPart as $key => $value) {
                if (in_array($key, $validOptions)) {
                    $settings[$repository][$branch][$key] = $value;
                    if ($key == "secret_access_token") {
                        $settings["secrets"][] = $value;
                    }
                }
            }
        }
    }

    $settings = cascade_into_next_level($configPart, $settings, $repository, $branch);
    return $settings;
}


// Hash Function
// If the default hash function doesn't exist in the environment then create it
// ref: http://php.net/manual/en/function.hash-equals.php#115635
if (!function_exists('hash_equals')) {
    function hash_equals($str1, $str2)
    {
        if (strlen($str1) != strlen($str2)) {
            return false;
        } else {
            $res = $str1 ^ $str2;
            $ret = 0;
            for ($i = strlen($res) - 1; $i >= 0; $i--) {
                $ret |= ord($res[$i]);
            }
            return !$ret;
        }
    }
}

/**
 * @param array $settings
 * @param array $repo
 * @param array $branch
 * @return array Merged settings for branch
 */
function findBranchSettings($settings, $repo, $branch)
{
    global $default, $repoDefault, $branchDefault;

    $option_used = array_merge(
        $default,
        $settings[$repoDefault][$branchDefault],
        $settings[$repo][$branchDefault],
        $settings[$repo][$branch]
    );

    return $option_used;
};
