# ![BigBrotherBot](http://i.imgur.com/7sljo4G.png) Echelon 2 Beta (v2.3)

### If you have older versions of Echelon 2, its recommended that you perform a clean installation. (delete everything and install from scratch)

Echelon is a web investigation tool for B3 administrators to study detailed statistics and other helpful information: about the B3 bot; game server clients; and admins. Echelon v.2 is not all about just inspecting information, it is also about interacting with the B3 bot; the gameserver; and the B3 MySQL database.

![Screenshot1](https://i.gyazo.com/79d9649bd3c2e6944cad3f332c8ea0cd.png)
![Screenshot1](https://i.gyazo.com/09150cd6fe48886237e06c2635a3a3c0.png)


NOTE: E-Mail feature is mostly disabled

# Changelog
## [2.3] - 21-12-2018
### Added
- User can set timezone under Settings --> My Account. 
- ported Toplist Penalties from Echelon v1
- Added icons

### Changed
- fixed Greeting, IP-Alias Search
- Navigation bar arrangement
- Maximum tempban duration has been limited to 1 day. Site Admins will be able to customize maximum ban duration in the future.



## [2.2] - 12-09-2018
### Added
- ported Alias Table Search from Echelon v1 and added IP-Alias Table Search (only working when explicitly selected in search-dropdown)
- RCON Live Kick (Player will get kicked from servers, if banned on Echelon)


## [2.1] - 10-09-2018
### Added
- IP-Aliases Feature

### Changed
- Fixed a lot of bugs
- removed old design and replaced with Bootstrap 4


## Echelon Development v2 ##
All the files are copyrighted by WatchMiltan, Eire.32 (eire32designs.com) and Bigbrotherbot (bigbrotherbot.com)

## Requirements ##
- Webserver (Aphace currently, other support coming soon)
- Version 5+ PHP
- MySQL DB (your B3 DB will work, but a seperate  one is advised)
- A MySQL user with connection rights to your B3 databases
- RCON details for the servers running B3 (RCON support is currently being phased out of Echelon)

## Installation ##
// This is by no means a comprehensive guide, it is a quick guide to get any of you started
- Create a MySQL user to connect your B3 database from your Webserver
- Run the echelon.sql file on your database to create the Echelon tables
- Go to http://example.com/path/echelon/ and follow the installer
- Delete the install folder once the web installer is done
- Login to Echelon using the credentials that were emailed to you
- Setup and config your Echelon to your needs

## NOTE ##
Please understand that there are large portions of Echelon that are unfinished. Please check back to this repo for the latest version.
There is also spotty support for BFBC2, (rcon will not work and will most likely error)
