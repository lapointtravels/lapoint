# Lapoint Web

### Prerequisites
Before getting started with setting up the Lapoint project please make sure you have following installed:
- MySQL
- WP-CLI
- PHP version 7.0 or later

### Quick Guide

Run `git clone git@github.com:lapointtravels/lapoint.git`
Run `cd lapoint`
Run `wp core download`
Edit your `wp-config.php` file to accomodate to your development environment.
Visit WPEngine to download a MySQL dump to import to your development DB.

### Detailed Guide

##### 1. Setup
1. Install the `wp-cli`. Follow the instructions on http://wp-cli.org/. Note: You don't need to have WordPress installed before.
2. Run `git clone git@github.com:lapointtravels/lapoint.git`.
3. Enter the project directory and run `wp core download`. 

##### 2. Database

1. To get a dump of the staging DB, go to [phpMyAdmin](https://my.wpengine.com/installs/lapoint/phpmyadmin) and select the Export. Select **Custom** as export method and **snapshot_lapoint**  in the databases section. Apart from that you can use the default settings. Click **GO**.
2. Create a local MySQL db (suggested name: lapoint_web) and import the export.
3. Open the `wp-config.php` in the project root. Update the DB config to match your setup -  `DB_NAME`, `DB_USER`, `DB_PASSWORD`.

##### 3. Get the project images

Since the `/uploads` directory is huge in this project it is currently git ignored. This means you have to get it yourself manually if you want things to look neat, preferably accessing WP Engine with SFTP. The `/uploads` directory is available in `/wp-content`. Get it and move it to the corresponding place in the project.

##### 4. Run the project

Run `wp server` in the console to run the project.

To run the project with full capability in a local environment we need to use ngrok. It creates a local domain name proxy so you can test language sub-domains, SSL and other coolio stuff in your development environment.

[Download ngrok](https://ngrok.com/), install as per instructions in their docs.

[Sign in](https://dashboard.ngrok.com/user/login) using your GitHub account and add the auth token as instructed.

In a new console tab, run `ngrok http 8080` to proxy your WP server to nrok.
Once ngrok is initiated you will receive the url to use.

... and you're done! :)

### Workflows and deployment

For everyone's convinience follow the workflow below when working with the Lapoint web project.
1. Check out a new branch everytime you initiating some kind of work. **Avoid working directly in master branch!**
2. When your work is ready push the branch and create a pull request.
3. If working in pair/group please review each other's changes.
4. Do a squash merge into master and **add a proper message of what is done**.
5. Pull master branch locally and then run `git push staging master` to push it to the staging environment on WP engine.
6. Let Lapoint verify the changes on http://lapoint.staging.wpengine.com.
7. If changes are accepted, [promote staging to production](https://lapoint.wpengine.com/wp-admin/admin.php?page=wpengine-staging). Otherwise make additional changes using the workflow above.

### Notes

###### DEPLOYMENT
In order to be able to push to the WP engine environment you need to [add your public SSH key](https://my.wpengine.com/installs/lapoint/git_push).

###### SYNCING DB AND UPLOADS
 If you for some reason get a new dump of the staging DB you might need to also fetch the latest version of the `/uploads` directory to keep everything in sync.

###### ENVIRONMENTS
**Staging:** http://lapoint.staging.wpengine.com

**Production:** http://lapoint.wpengine.com

To access the admin console locally use the same credentials as in the production environment.

###### WPML
The WMPL plugins needs to be installed locally. Currently following plugins are used:
- wpml-sticky-links
- wmpl-string-translation
- wpml-translation-management
- wpml-widgets
















