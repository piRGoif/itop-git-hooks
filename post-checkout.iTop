#!/usr/bin/php
<?php
/**
 * post-checkout Git hook for iTop dev
 *
 * usage : create a .git/hooks/post-checkout symlink pointing to this file
 *
 * What it does : restore iTop dirs per branches : conf/, data/, env-production/
 * Backups are made prefixing dirs names with ".BKP-HOOK."
 * If backup does not exists, create empty dirs so that setup run is needed
 * If backup exists then simple symlink are created
 */


echo "## iTop DEV Git Hook ##\n";


$repoRoot = execGitCmd('git rev-parse --show-toplevel');
echo "repo root=$repoRoot\n";


$repoBranchSource = execGitCmd('git rev-parse --abbrev-ref @{-1}');
$repoBranchTarget = execGitCmd('git rev-parse --abbrev-ref head');

echo "previous branch=$repoBranchSource\n";
echo "checked out branch=$repoBranchTarget\n";



/**
 * @param  string $sGitCommand
 * @return string command return value, stopping at the last letter (no newline car at the end)
 */
function execGitCmd($sGitCommand)
{
  $sOrigReturn = shell_exec($sGitCommand);
  return rtrim($sOrigReturn);
}
