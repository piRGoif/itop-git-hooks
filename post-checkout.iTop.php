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
const ITOP_DIRS = array('conf', 'data', 'env-production');


echo "## iTop DEV Git Hook ##\n";

$repoRoot = execGitCmd('git rev-parse --show-toplevel');
echo "repo root=$repoRoot\n";


$repoBranchSource = execGitCmd('git rev-parse --abbrev-ref @{-1}');
$repoBranchTarget = execGitCmd('git rev-parse --abbrev-ref head');

echo "previous branch=$repoBranchSource\n";
echo "checked out branch=$repoBranchTarget\n";

foreach (ITOP_DIRS as $sCurrentDir)
{
	$sCurrentPath = $repoRoot.'/'.$sCurrentDir;

	$bIsCurrentConfSymlink = is_link($sConfDir);
	if (!$bIsCurrentConfSymlink)
	{
		backupDir($repoRoot, $sDirName, $repoBranchSource);
	}

	// target exists ?
	// N : create
	// Y : get path

	// create/update symlink
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
 * Move dir as backup (adds prefix)
 *
 * @param  string $sRepoRoot
 * @param  string $sDirName
 * @param  string $sBranchName
 */
function backupDir($sRepoRoot, $sDirName, $sBranchName)
{
	$sBranchNamePath = str_replace('/', '--', $sBranchName);
	$sDirSourcePath = $sRepoRoot.'/'.$sDirName;
	$sDirBackupPath = $sRepoRoot.'/.BKP-'.$sBranchNamePath.'.'.$sDirName;
	rename($sDirSourcePath, $sDirBackupPath);
}
