#!/usr/bin/php
<?php
/**
 * post-checkout Git hook for iTop dev
 *
 * usage : create a .git/hooks/post-checkout symlink pointing to this file
 *
 * What it does : restore iTop dirs per branches : conf/, data/, env-production/
 * Backups are made prefixing dirs names with ".BKP-<branch>.<dir>"
 * If backup does not exists, create empty dirs so that setup run is needed
 * If backup exists then simple symlink are created
 */


const BKP_DIR = '.CONF-BKP';
const ITOP_DIRS = array('conf', 'env-production');

main();


function main()
{
	echo "## iTop DEV Git Hook ##\n";

	$sRepoRoot = execGitCmd('git rev-parse --show-toplevel');
	echo "repo root=$sRepoRoot\n";


	$sSourceBranch = execGitCmd('git rev-parse --abbrev-ref @{-1}');
	$sTargetBranch = execGitCmd('git rev-parse --abbrev-ref head');
	echo "previous branch=$sSourceBranch\n";
	echo "checked out branch=$sTargetBranch\n";

	foreach (ITOP_DIRS as $sCurrentDir)
	{
		$sCurrentPath = $sRepoRoot.'/'.$sCurrentDir;

		echo "-- Path: $sCurrentPath...\n";
		$bIsCurrentPathExists = file_exists($sCurrentPath);
		if ($bIsCurrentPathExists)
		{
			$bIsCurrentPathSymlink = is_link($sCurrentPath);
			if ($bIsCurrentPathSymlink)
			{
				echo 'removing symlink...';
				// using rmdir instead of unlink as the symlink points to a directory
                // see https://stackoverflow.com/a/18262809/5114238
				rmdir($sCurrentPath);
				echo 'ok !';
			}
			else
			{
				$sCurrentSourceBkpPath = getConfDirBackup($sRepoRoot, $sSourceBranch, $sCurrentDir);
				echo "backing up to $sCurrentSourceBkpPath...";
				@chmod($sCurrentPath, 0777); // conf might be protected !
				rename($sCurrentPath, $sCurrentSourceBkpPath);
				echo 'ok !';
			}
		}
		else
		{
			echo 'doesn\'t exist !';
		}
		echo "\n";

		$sCurrentTargetBkpPath = getConfDirBackup($sRepoRoot, $sTargetBranch, $sCurrentDir);
		echo "target $sCurrentTargetBkpPath...";
		if (!file_exists($sCurrentTargetBkpPath))
		{
			echo 'creating...';
			mkdir($sCurrentTargetBkpPath);
			echo 'ok !';
		}
		echo "\n";

		// create symlink
		echo 'creating symlink...';
		symlink($sCurrentTargetBkpPath, $sCurrentPath);
		echo "ok !\n";
	}
}


/**
 * @param  string $sGitCommand
 *
 * @return string command return value, stopping at the last letter (no newline car at the end)
 */
function execGitCmd($sGitCommand)
{
	$sOrigReturn = shell_exec($sGitCommand);

	return rtrim($sOrigReturn);
}


/**
 * @param  string $sRepoRoot
 * @param  string $sBranchName
 * @param  string $sDirName
 *
 * @return string
 */
function getConfDirBackup($sRepoRoot, $sBranchName, $sDirName)
{
	$sConfBkpPath = $sRepoRoot.'/'.BKP_DIR;
	if (!file_exists($sConfBkpPath))
	{
		mkdir($sConfBkpPath);
	}

	$sBranchNameForPath = getBranchNameForPath($sBranchName);
	$sBranchBkpPath = $sConfBkpPath.'/'.$sBranchNameForPath;
	if (!file_exists($sBranchBkpPath))
	{
		mkdir($sBranchBkpPath);
	}

	$sDirBackupPath = $sBranchBkpPath.'/'.$sDirName;

	return $sDirBackupPath;
}

/**
 * @param string $sBranchName
 *
 * @return string
 */
function getBranchNameForPath($sBranchName)
{
	$sBranchNameForPath = str_replace('/', '--', $sBranchName);

	return $sBranchNameForPath;
}
