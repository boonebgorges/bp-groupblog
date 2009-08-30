=== BuddyPress Groupblog ===
Contributors: Rodney Blevins & Marius Ooms
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7374704
Tags: buddypress,groups,blogs,content
Requires at least: WPMU 2.8 / BP 1.1
Tested up to: WPMU 2.8.4a
Stable tag: 1.1.4

BuddyPress Groupblog extends the group functionality by enabling the group to have a single blog associated with it. Group members are automatically added to the blog and will have roles as set by the group admin.

== Description ==

**NOTE: This plugin requires BuddyPress 1.1, released September, 2009. See the FAQ why we build it on the upcoming BuddyPress version.**

The BuddyPress Groupblog plugin extends the group functionality by enabling each group to have a single blog associated with it. Group members are automatically added to the blog and will have blog roles as set by the groupblog admin settings.

**Features:**

* Automated blog registration at group creation stage.
* Blog privacy settings are initially inherited from group privacy settings.
* Group members are automatically added to the blog.
* Blog roles match group roles as set by the group admin.
* Solid error checking that the blog follows validation.
* Group admin tab to access the group-blog settings.
* Recent posts are displayed on the group home page, much like the forum topics.
* A menu tab is added to display the latest blog activity and blog page links.
* Blog themes will have the ability to pull in group info and create a theme that could ressemble the group exactly.
* Leaving the group will downgrade the member role to 'subscriber'.
* Allow the group admin to select one of his/her existing blogs.

**Roadmap:**

* Include an RSS icon for easy access to the Blog's RSS feed.
* More templates, e.g. a display pages, categories, comments, etc.

**Known issues:**

* Currently the selected member roles are not saving at creation stage. They are updated when you re-save in the admin settings.

== Installation ==

NOTE: After plugin installation, you MUST move the "groupblog" templates folder to your bp active member theme. See below.

1) unzip the bp-groupblog.zip file into `/wp-contents/plugins/bp-groupblog`

2) activate the plugin

3) You are done!

4) If you want to override the theme files, just copy the 'groupblog' folder into your active theme and modify to your needs.

NOTE: You *must* activate this component AFTER bp has been activated.

== Frequently Asked Questions ==

= Why did you build on a version of BuddyPress that's not yet released? =

This plugin requires the Group API included in BuddyPress 1.1. This API is needed to hook into the creation stages of the group. It also simplifies development for plugins extending group functionality.

== Screenshots ==

1. Screenshot of the group blog creation stage.
2. Screenshot of the group blog page.

== Changelog ==
* Fixed a bug which would cause the group creator to be demoted to subscriber of his own blog if member blogging 

= 1.1.4 =
* Added file_exists to various files, so templates can be overriden in the theme. This also means we had to move the resource folders back into the theme folder.

= 1.1.3 =
* Now using plugin-template.php, so there is no longer a need to move the groupblog folder to the active theme folder. Alos updating the plugin through the wordpress plugin interface now works without problems.

= 1.1.2 = 
* Updated language files.

= 1.1.1 = 
* Improved jquery handeling on check boxes and radio buttons.

= 1.1 =
* If a member leaves the group they will now be downgraded to 'subscriber'.
* Added template tags: groupblog_blog_id($group_id), get_groupblog_blog_id($group_id), groupblog_group_id($blog_id), get_groupblog_group_id($blog_id)
* Group admin now has the option of either creating a new blog or associating one of his blogs with the new group.
* Moved css, images and js folders to the theme folder.
* Added Jquery to give feedback to user input.

= 1.0 =
* Initial release.

= The changelog can also be found here: =
http://plugins.trac.wordpress.org/log/bp-groupblog?verbose=on