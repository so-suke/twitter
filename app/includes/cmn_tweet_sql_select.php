<?php

$retweet_sql_select = "t.id as id, r.user_id, t.text, tu.name as user_name, tu.screen_name as user_screen_name,
		ru.name as 'retweet_user_name', ru.screen_name as 'retweet_user_screen_name','retweet' as tweet_kind, p.avatar_img_path, r.created_at AS 'at_for_sort'";

$tweet_sql_select = "t.id as id, t.user_id, t.text, u.name as user_name, u.screen_name as user_screen_name,
		NULL as retweet_user_name, NULL as retweet_user_screen_name, 'tweet' as tweet_kind, p.avatar_img_path, t.created_at AS 'at_for_sort'";
