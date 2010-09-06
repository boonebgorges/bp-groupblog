=== BuddyPress Groupblog ===
Contributors: MariusOoms
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7374704
Tags: buddypress,groups,blogs,content
Requires at least: WP 3.0 / BP 1.2
Tested up to: WP 3.0.1
Stable tag: 1.4.6

BuddyPress Groupblog extends the group functionality by enabling the group to have a single blog associated with it. Group members are automatically added to the blog and will have roles as set by the group admin.

== Description ==

The BuddyPress Groupblog plugin extends the group functionality by enabling each group to have a single blog associated with it. Group members are automatically added to the blog and will have blog roles as set by the groupblog admin settings.

**Features:**

* P2 integration and frontend posting.
* Admin can set Template specific groupblogs.
* Full blog theme integration. The included bp-groupblog theme mimics the group pages.
* WP Admin option to set default blog for groups plus bonus options.
* Automated blog registration at group creation stage.
* Bypass default blog validation to allow dashes, underscores, numeral only and minimum character count.
* Blog privacy settings are initially inherited from group privacy settings.
* Group members are automatically added to the blog.
* Blog roles match group roles as set by the group admin.
* Solid error checking that the blog follows validation.
* Group admin tab to access the group-blog settings.
* Recent posts are displayed on the group home page, much like the forum topics.
* A menu tab is added to display the latest blog activity and blog page links.
* Blog themes will have the ability to pull in group info and create a theme that could resemble the group exactly.
* Leaving the group will downgrade the member role to 'subscriber'.
* Allow the group admin to select one of his/her existing blogs.
* A new ajax backend.

**Roadmap:**

* Allow the admin to let group admins choose the blog name, instead of following the group name.
* Include an RSS icon for easy access to the Blog's RSS feed.

== Installation ==

1) unzip the bp-groupblog.zip file into `/wp-contents/plugins/bp-groupblog`

2) move the `/themes/...` to your WP themes folder

3) activate the plugin

4) You are done!

**NOTE: Please deactivate the plugin before running automatic upgrade or you will get a big fat 'Cannot redeclare' fatal error. Regardless, if you do activate while the plugin is active it will still work fine. It is just that nobody likes errors, even when they are not real.**

== Other Notes ==

**Known Issues:**

* In order for Group Avatars to show on blogs please adjust the bp-core-avatars.php file. I realize this is a nono, but I don't see another way. Patches and ideas are welcome. For know adjust the last two functions for plugins/buddypress/bp-core/bp-core-avatars.php to the following:

`
/**
 * bp_core_avatar_upload_path()
 *
 * Returns the absolute upload path for the WP installation
 *
 * @global object $current_blog Current blog information
 * @uses wp_upload_dir To get upload directory info
 * @return string Absolute path to WP upload directory
 */
function bp_core_avatar_upload_path() {
	global $current_blog;

	// Get upload directory information from current site
	$upload_dir = wp_upload_dir();

	// If multisite, and current blog does not match root blog, make adjustments
	if ( bp_core_is_multisite() && BP_ROOT_BLOG != $current_blog->blog_id )
		$upload_dir['basedir'] = WP_CONTENT_DIR . '/uploads/';

	return apply_filters( 'bp_core_avatar_upload_path', $upload_dir['basedir'] );
}

/**
 * bp_core_avatar_url()
 *
 * Returns the raw base URL for root site upload location
 *
 * @global object $current_blog Current blog information
 * @uses wp_upload_dir To get upload directory info
 * @return string Full URL to current upload location
 */
function bp_core_avatar_url() {
	global $current_blog;

	// Get upload directory information from current site
	$upload_dir = wp_upload_dir();

	// If multisite, and current blog does not match root blog, make adjustments
	if ( bp_core_is_multisite() && BP_ROOT_BLOG != $current_blog->blog_id )
		$upload_dir['baseurl'] = WP_CONTENT_URL . '/uploads';

	return apply_filters( 'bp_core_avatar_url', $upload_dir['baseurl'] );
}
`

== Screenshots ==

1. Screenshot of the group blog creation stage.
2. Screenshot of the group blog page.

== Changelog == 

= 1.4.6 =
* Fixed users being added properly to the group blog.
* Fixed user rights applied to correct blog. The main blog is no longer affected.
* Fixed hidden and private groups to allow member joining.

= 1.4.5 =
* Overhaul of the admin section
* Inclusion for P2 support
* Variety of new options, including template control
* Made compatible with 3.0 asaik

= 1.4.4 =
* Sorry I neglected this plugin for a while and did not transcribe the changes.

= 1.4.3 =
* Restructed templating. To control the sidebar of your group, you will need to move the bp-groupblog/groupblog folder to buddypress/bp-themes/bp-default/.

= 1.4.2 =
* Changed group template behavior and structure
* Added admin option to redirect to Blog Home within the Group

= 1.4.1 =
* Groupblog were not created for existing groups, now fixed
* Fixing the issue above also solved main blog posts within groups

= 1.4 =
* Made it compatible with WPMU 2.9 / BP 1.2
* Moved the moment when the blog is actually created to the group save step
* Updated the included theme to reflect the new BP Default theme
* Added new admin option, such validation overrides and redirect option

= 1.3.1 =
* Prevented group creator from demoting him/herself to anything lower than administrator.

= 1.3 =
* Reworked all the code regarding adding users to the groupblog
* Users are now immediately added on group join (No more visiting the blog first!)
* Promoting, Demoting, Banning and Unbanning directly adjusts the member permissions
* **Big thanks goes out to Boone for developing much of the needed code!**

= 1.2.4 =
* Added missing registration hook, to ensure default settings are set.

= 1.2.3 =
* Removed unnecessary code, fixing mysql errors.
* Added Blog links menu.
* Changed the local to use 'groupblog' in group template files.

= 1.2.2 =
* Added a function to check BuddyPress is loaded first

= 1.2.1 =
Updated language files

= 1.2 =
* Added admin settings screen. You can now set the default groupblog theme, plus some bonus options.
* Changed some code here and there.
* Including a groupblog theme based on the bp-sn-parent theme. You can use this theme to make wpmu blogs resemble the BuddyPress group and display group related content inside the groupblog theme.

= 1.1.6 =
* Added a message to inform the user that a groupblog is only chosen once.

= 1.1.5 =
* Fixed a bug where the member roles were not being updated upon group creation.

= 1.1.4 =
* Added file_exists to various files, so templates can be overridden in the theme. This also means we had to move the resource folders back into the theme folder.

= 1.1.3 =
* Now using plugin-template.php, so there is no longer a need to move the groupblog folder to the active theme folder. Also updating the plugin through the wordpress plugin interface now works without problems.

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
* Fixed a bug which would cause the group creator to be demoted to subscriber of his own blog if member blogging.

= 1.0 =
* Initial release.

= The changelog can also be found here: =
http://plugins.trac.wordpress.org/log/bp-groupblog?verbose=on