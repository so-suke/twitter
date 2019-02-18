-- シンプルにフォロワーidを取得。
select f.from_user_id
from follows as f
where f.to_user_id = 1

-- フォロワーを取得, フォローしているかを取得。
-- select f.from_user_id, u.name as u_name, u.screen_name as u_screen_name, p.text as p_text, (
-- 	select 1
-- 	from follows as f2
-- 	where f2.to_user_id = f.from_user_id
-- 	and f2.from_user_id = 1
-- ) is_auth_following_ifnotnull, 1 as is_auth_follower_ifnotnull
-- from follows as f
-- join users as u on f.from_user_id = u.id
-- and f.to_user_id = 1
-- join profilese as p on u.id = p.user_id

-- ログインユーザを1とし、プロフィールページのユーザのIDを2とした場合、フォローしている人がフォロワーかどうかも取得したい。
select f.from_user_id, u.name as u_name, u.screen_name as u_screen_name, p.text as p_text, (
	select 1
	from follows as f2
	where f2.to_user_id = f.from_user_id
	-- ログインユーザid
	and f2.from_user_id = 1
) is_auth_following_ifnotnull, (
	select 1
	from follows as f3
	where f3.from_user_id = f.from_user_id
	-- ログインユーザid
	and f3.to_user_id = 1
) is_auth_follower_ifnotnull
from follows as f
join users as u on f.from_user_id = u.id
and f.to_user_id = 2
join profilese as p on u.id = p.user_id