# Git hooks for iTop


## ❓ Goal
Those hooks aims to ease developing on [iTop](https://github.com/Combodo/iTop).

As each GIT branch for this project can hold distinct datamodels, installed extensions etc., the hooks will automatically save and switch
 config.


## ☑ Available hooks

For now only post-checkout [hook](https://git-scm.com/docs/githooks) is implemented.

On each checkout, the hook will :

* if source config files exists then move them to a backup
* if target config files don't exists create them
* if config symlink exists remove it 
* create a config symlink to target config files

Backups contains :

* `/conf/` folder
* `/env-production/` folder

Backups are named like this : `.CONF-BKP/<source_branch>/<dir_name>`.  
Exemple : `.CONF-BKP/develop/data`. 


## 🏭️ TODO

* automatically add ignore on /.CONF-BKP
* handle `/data/production.delta.xml`
* handle `/data/production-modules`
* delete branch, including fetch/pull prune (no existing hook for this...)
* rename branch (no existing hook either :/)
