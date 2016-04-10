# Git webhook deploy, 100% in php
[![Code Climate](https://codeclimate.com/github/IanEdington/deploy-php/badges/gpa.svg)](https://codeclimate.com/github/IanEdington/deploy-php) 

100php-git-deploy is an easily configured deployment tool for php only shared hosting.

A json config file tells 100php-git-deploy where to put a specific repo. `config.php` can be as little as repo or can be much more flexible.

Required settings:
```javascript
{
    "repository": "", // url of repo to be deployed
    "target_dir": "" // url of deployment directory
}
```

Other settings:
```javascript
{
    "branch": "master", // branch to be deployed
    "secret_access_token": "", // used by github for security
    "git_path": "git", // file path to git executable
    "time_limit": 30, //
    "backup_dir": "",
    "clean_up": "",
    "email_on_error": "",
}
```

composer deployment:
```javascript
    composer: false, // if this is true composer will be checked as a requirement
    composer_path: "composer", // 
    composer_options: "",
    composer_home, "",
```

rsync deployment:
```javascript
{
    "rsync": false, // use rsync or not
    "rsync_path": "rsync", // path to rsync executable
    "staging_dir": false, // path to local staging directory
    "exclude": [], // array of files to exclude from rsync.
    "version_file": false, // should it be included?
    "delete_files": false, // This determins if rsync will delete files from the deployment environment when they are deleted from the git repo. If your EXCLUDE array is properly configured you should set this to TRUE.
}
```
