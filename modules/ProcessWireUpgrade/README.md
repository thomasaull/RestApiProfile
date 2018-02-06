# ProcessWire Upgrade

Provides core and module upgrade notifications and optionally
installation from the admin. 

Can be used to upgrade your ProcessWire core or any module that
is available from http://modules.processwire.com.

## Please note before using this tool

Files installed by this tool are readable and writable by Apache,
which may be a security problem in some hosting environments. 
Especially shared hosting environments where Apache runs as the
same user across all hosting accounts. If in doubt, you should
instead install core upgrades and/or modules and module upgrades
manually through your hosting account (FTP, SSH, etc.), which
is already very simple to do. This ensures that any installed files 
are owned and writable by your user account rather than Apache.

## Core Upgrades

This tool checks if upgrades are available for your ProcessWire installation. 
If available, it will download the update. If your file system is
writable, it will install the update for you. If your file system is
not writable, then it will install upgrade files in a writable 
location (under /site/assets/cache/) and give you instructions on 
what files to move. 

Options to upgrade from the master or dev branch are available. 

This utility can be also used to upgrade any PW 2.5.20+ or newer 
site to the latest version.

This utility makes versioned backup copies of any files it 
overwrites during the upgrade. Should an upgrade fail for some
reason, you can manually restore from the backups should you
need to. 

If your ProcessWire version is new enough to have the 
WireDatabaseBackup class (PW 2.5.14+) then this module will
also give you the option of backing up your database. 

After installing a core upgrade, you may want to manually update
the permissions of installed files to be non-writable to Apache,
depending on your environment. 


## Module Upgrades

Uses web services from modules.processwire.com to compare your
current installed versions of modules to the latest remote 
versions available. Provides upgrade links when it finds newer 
versions of modules you have installed. 

After installing module upgrades, you may want to manually update
the permissions of installed files to be non-writable to Apache,
depending on your environment. 


## Requirements

- ProcessWire 2.5.20 or newer 


