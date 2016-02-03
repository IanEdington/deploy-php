The idea of this project is to create a php git server that will deploy any branches qued for deployment to the correct folders.

The deployment folder should be a yaml or json document called deployment-config.yaml / .json and should contain a extreamly flexible deployment structure. Usablility would probably benefit from cascading object.

possible structure:
```yaml
property
github key:
hooked repository
	property1
	property3
	brach: ^latest // what is the consequence of this?
	deployment1
		property
		branch:
	deployment2
		property
		branch: ^lattest
hooked repository #2
	deployment1
		...
```
... most hosts don't have yaml installed so better to use json.

Required properties:
	REMOTE_REPOSITORY
	BRANCH
	TARGET_DIR
	STAGING_DIR

Suggested properties:
	SECRET_ACCESS_TOKEN
	EXCLUDE - array of files to exclude from rsync. Any values in higher levels will be added to this array.
	DELETE_FILES - TRUE or FALSE. This determins if rsync will delete files from the deployment environment when they are not in the git repo. If your EXCLUDE array is properly configured you should set this to TRUE.

other:
	VERSION_FILE?
	TIME_LIMIT
	BACKUP_DIR
	COMPOSER - if this is true composer will be checked as a requirement
	COMPOSER_OPTIONS
	COMPOSER_HOME
	CLEAN_UP
	EMAIL_ON_ERROR

Process


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

