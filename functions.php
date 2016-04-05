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
    list ($repository, $branch) = find_branch_and_repo($configPart, $repository, $branch);

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

        foreach ($configPart as $key => $value) {
            if (in_array($key, $validOptions)) {
                $settings[$repository][$branch][$key] = $value;
            }
        }
    }

    $settings = cascade_into_next_level($configPart, $settings, $repository, $branch);
    return $settings;
}
