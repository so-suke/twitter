select t.text, tu.name as user_name, tu.screen_name as user_screen_name, ru.name as 'retweet_user_name', ru.screen_name as 'retweet_user_screen_name','retweet' as tweet_kind, r.created_at AS 'at_for_sort'
from retweets as r
join tweets as t on r.tweet_id = t.id
join users as tu on t.user_id = tu.id
join users as ru on r.user_id = ru.id
and ru.user_id = 1

リツイートのユーザidが自分のフォローしている人または自分。

-- トップ画面のリツイート取得。
select r.user_id, t.text, tu.name as user_name, tu.screen_name as user_screen_name, ru.name as 'retweet_user_name', ru.screen_name as 'retweet_user_screen_name','retweet' as tweet_kind, r.created_at AS 'at_for_sort'
from retweets as r
join tweets as t on r.tweet_id = t.id
and (r.user_id in (
	select to_user_id
	from follows
	WHERE from_user_id = 1
)
or r.user_id = 1)
join users as tu on t.user_id = tu.id
join users as ru on r.user_id = ru.id

-- トップ画面のツイート取得。
-- select t.user_id, t.text, u.name as user_name, u.screen_name as user_screen_name, NULL as retweet_user_name, NULL as retweet_user_screen_name, 'tweet' as tweet_kind, t.created_at AS 'at_for_sort'
-- from tweets as t
-- join users as u on t.user_id = u.id
-- where t.user_id in (
-- 	SELECT to_user_id
-- 	FROM follows
-- 	WHERE from_user_id = 1
-- )
-- or t.user_id = 1

select t.user_id, t.text, u.name as user_name, u.screen_name as user_screen_name, NULL as retweet_user_name, NULL as retweet_user_screen_name, 'tweet' as tweet_kind, t.created_at AS 'at_for_sort'
from tweets as t
join users as u on t.user_id = u.id
and (t.user_id in (
	SELECT to_user_id
	FROM follows
	WHERE from_user_id = 1
)
or t.user_id = 1)