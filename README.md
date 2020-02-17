RSVP Events
====

CONTENTS OF THIS FILE
---------------------   
* Introduction
* Architecture
* Requirements
* Installation
* Configuration
* Assumption
* Maintainers

INTRODUCTION
------------

RSVP Events module provides an Event content type that allows users to create 
events adding a Title, Description and the Venue for the Event. This module 
also provides the RSVP form for the authenticated users to signup for 
the particular Event. 


ARCHITECTURE
------------
RSVP Form module provides followings features,
1. Event content type with following fields,
* Title
* Description
* Venue
2. Entity: RSVPConfirmations 
The entity RSVPConfirmations stores the relation between the user and the Event.
3. RSVP form pseudo field
This field is for adding the RSVP form on the Event display format.
4. Reports page listing RSVP'd users with Event Title and users email address


REQUIREMENTS	
------------

This module requires the following modules:
* Views (https://www.drupal.org/project/views)


INSTALLATION
------------

1. This project installs like any other Drupal module. There is extensive
documentation on how to do this here:
https://drupal.org/documentation/install/modules-themes/modules-8 
But essentially:nDownload the archive from https://github.com/shinde-rahul/rsvp
and expand it into the modules/ directory in your Drupal 8 installation.

2. Within Drupal, enable any Example sub-module you wish to explore in Admin
menu > Extend.

You can use composer to download this module and drush to enable it,
```
composer require shinde-rahul\rsvp_event
drush en rsvp_event -y
```


CONFIGURATION
-------------

* Update Geolocation settings
    - Goto Configuration >> Web services (admin/config/services/geolocation)
    - Add Google Maps API key


ASSUMPTIONS
-----------

* Only authenticated users can RSVP for an event.
* For anonymous user it should show message for signup.
* Distance is calculated based on the longitude and latitude attached to  
the user and event.
* The longitude and latitude for user is manual.



MAINTAINERS
-----------

Current maintainers:
* Rahul A. Shinde (rahul.shinde) - https://www.drupal.org/u/rahulshinde
