I don't know how to use Git.

# grape #
A Miiverse clone, named which due to Miiverse's original codename being 'olive'.
Heavy work in progress.
There is a lot of naive programming in here, I'm new to PHP and it'll get better soon.

# grp_portal #
This is portal, or the Wii U mode.
# grplib #
This will be a shared library to be used between 3DS and off-device when they are made.

# How to install??? #
This section would be written, but manual database creation is required at the moment :(

# Rewrites (nginx) #
These are required for good functionality.

rewrite ^/titles/([A-Za-z0-9]+)$ /titles.php?title_id=$1;
rewrite ^/titles/([A-Za-z0-9]+)/([A-Za-z0-9]+)$ /titles.php?title_id=$1&community_id=$2;
rewrite ^/titles/([A-Za-z0-9]+)/([A-Za-z0-9]+)/new$ /titles.php?title_id=$1&community_id=$2;
rewrite ^/titles/([A-Za-z0-9]+)/([A-Za-z0-9]+)/([A-Za-z0-9]+)$ /titles.php?title_id=$1&community_id=$2&mode=$3;
rewrite ^/theme-set$ /theme-set.php last;
rewrite ^/settings/played_title_ids$ /my/played.php last;
rewrite ^/$ /root.php last;
rewrite ^/my/latest_following_related_profile_posts$ /my/follow-rel-posts.php last;
rewrite ^/settings/profile_post.unset.json$ /profile-post-unset.php last;
rewrite ^/check_update.json$ /check_update.php last;
rewrite ^/settings/tutorial_post$ /tutorial_post.php last;
rewrite ^/friend_messages$ /messages.php last;
rewrite ^/friend_messages/([A-Za-z0-9_-]+)$ /messages.php?user_id=$1 last;
rewrite ^/news/my_news$ /news.php last;
rewrite ^/news/friend_requests$ /friendrequests.php last;
rewrite ^/users/friend_request.accept.json$ /friend_request.php last;
rewrite ^/users/friend_request.cancel.json$ /friend_request.php?cancel last;
rewrite ^/users/friend_request.delete.json$ /friend_request.php?delete last;
rewrite ^/users/breakup.json$ /friend_request.php?breakup last;
rewrite ^/users$ /user-search.php last;
rewrite ^/users/show$ /user-show.php last;
rewrite ^/warning/deleted_account$ /content/warnings/act_deleted.php last;
rewrite ^/warning/readonly$ /content/warnings/readonly.php last;
rewrite ^/communities$ /communities.php last;
rewrite ^/communities/favorites$ /communities-showfavorites.php last;
rewrite ^/titles/([A-Za-z0-9]+)/([A-Za-z0-9]+)/favorite.json$ /communities-createfavorite.php?olive_community_id=$2 last;
rewrite ^/titles/([A-Za-z0-9]+)/([A-Za-z0-9]+)/unfavorite.json$ /communities-createfavorite.php?olive_community_id=$2&delete last;
rewrite ^/identified_user_posts$ /identified_user_posts.php last;
rewrite ^/guest_menu$ /guest_menu.php last;
rewrite ^/my_menu$ /my_menu.php last;
rewrite ^/act/create$ /act_create-form.php last;
rewrite ^/act/login$ /act_login.php last;
rewrite ^/act/logout$ /act_logout.php last;
rewrite ^/admin/titles_create$ /create_title.php last;
rewrite ^/admin/communities_create$ /create_community.php last;
rewrite ^/settings/profile /profile_settings.php last;
rewrite ^/login$ /login.php last;
rewrite ^/people$ /people.php last;
rewrite ^/profiles$ /profile_create.php last;
rewrite ^/posts$ /post-create.php last;
rewrite ^/posts/([A-Za-z0-9_-]+)$ /posts.php?id=$1 last;
rewrite ^/posts/([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)$ /posts.php?id=$1&mode=$2 last;
rewrite ^/replies/([A-Za-z0-9_-]+)$ /replies.php?id=$1 last;
rewrite ^/replies/([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)$ /replies.php?id=$1&mode=$2 last;
rewrite ^/users/([A-Za-z0-9_-]+)$ /users.php?user_id=$1 last;
rewrite ^/users/([A-Za-z0-9_-]+)/([A-Za-z0-9]+)$ /users.php?user_id=$1&mode=$2 last;
rewrite ^/users/@me$ /profile-me.php last;
rewrite ^/users/([A-Za-z0-9_-]+)/friend_request.create.json$ /friend_request.php?create&user_id=$1 last;
rewrite ^/help_and_guide$ /help_and_guide.php last;
rewrite ^/special/redesign_announcement$ /content/special/redesign.php last;
rewrite ^/help* /content/help* last;
rewrite ^/faq* /content/help* last;