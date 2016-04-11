<?php
/*
 * Adapted from @markomarkovic's simple-php-git-deploy
 * ref:https://github.com/markomarkovic/simple-php-git-deploy
 */

function deploy($project)
{
    extract($project);

    //Does the server have all the deployTools it needs?
    $deployToolsNeeded = [$git_path];

    if ($composer == true) {
        $deployToolsNeeded[] = $composer_path;
    }

    //MAKE SURE IT'S SETUP properly
    foreach ($deployToolsNeeded as $deployTool) {
        $path = trim(shell_exec('builtin which '.$deployTool));
        if ($path == '') {
            echo "throw error"; //TODO Error Handling
        } else {
            $version = explode("\n", shell_exec($deployTool.' --version'));
            printf('- %s : %s'."\n", $path, $version[0]);
        }
    }

    echo "Environment OK.".PHP_EOL;

    echo <<<EOL

=============================================
deploying: $repo $branch
to:        $target_dir
EOL;


    // Build up the commands
    $commands = array();

    // git commands
    $git_base = "$git_path --git-dir=\"$target_dir.git\" --work-tree=\"$target_dir\" ";

    if (is_dir($target_dir)) {
        // TODO check that it's the right repo
        $commands[] = $git_base."fetch origin ".$branch;
        $commands[] = $git_base."reset --hard FETCH_HEAD";
    } else {
        $commands[] = "$git_path clone --depth=1 --branch \"$branch\" $repo_url $target_dir";
    }

    $commands[] = "$git_path submodule update --init --recursive";

    // Invoke composer
    if ($composer) {
        if ($composer_home && is_dir($composer_home)) {
            putenv('COMPOSER_HOME='.$composer_home);
        }
        $commands[] =
            "$composer_path --no-ansi -n --no-progress -d $target_dir install $composer_options";
    }


    // Run the commands
    $output = '';
    foreach ($commands as $command) {
        set_time_limit($time_limit);

        $tmp = array();
        exec($command.' 2>&1', $tmp, $return_code); // Execute the command

        printf(
            '$ %s %s',
            trim($command),
            trim(implode("\n", $tmp))
        );
        $output .= ob_get_contents();

        ob_flush(); // Try to output everything as it happens
    }
}
