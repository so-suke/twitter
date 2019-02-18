-- プロフィールページのユーザのIDを1とした場合、フォローしている人がフォロワーかどうかも取得したい。
-- select f.to_user_id, u.name as u_name, u.screen_name as u_screen_name, p.text as p_text, (
-- 	select 1
-- 	from follows as f2
-- 	where f2.from_user_id = f.to_user_id
-- 	and f2.to_user_id = 1
-- ) is_auth_follower_ifnotnull, 1 as is_auth_following_ifnotnull
-- from follows as f
-- join users as u on f.to_user_id = u.id
-- and f.from_user_id = 1
-- join profilese as p on u.id = p.user_id

-- ログインユーザを1とし、プロフィールページのユーザのIDを2とした場合、フォローしている人がフォロワーかどうかも取得したい。
select f.to_user_id, u.name as u_name, u.screen_name as u_screen_name, p.text as p_text, (
	select 1
	from follows as f2
	where f2.from_user_id = f.to_user_id
	and f2.to_user_id = 1
) is_auth_follower_ifnotnull, (
	select 1
	from follows as f3
	where f3.from_user_id = 1
	and f3.to_user_id = f.to_user_id
) as is_auth_following_ifnotnull
from follows as f
join users as u on f.to_user_id = u.id
and f.from_user_id = 2
join profilese as p on u.id = p.user_id

-- ログインユーザを1とし、プロフィールページのユーザのIDを1とした場合、フォローしている人がフォロワーかどうかも取得したい。
-- select f.to_user_id, u.name as u_name, u.screen_name as u_screen_name, p.text as p_text, (
-- 	select 1
-- 	from follows as f2
-- 	where f2.from_user_id = f.to_user_id
-- 	and f2.to_user_id = 1
-- ) is_auth_follower_ifnotnull, (
-- 	select 1
-- 	from follows as f3
-- 	where f3.from_user_id = 1
-- 	and f3.to_user_id = f.to_user_id
-- ) as is_auth_following_ifnotnull
-- from follows as f
-- join users as u on f.to_user_id = u.id
-- and f.from_user_id = 1
-- join profilese as p on u.id = p.user_id

-- 要件通りのis_follower, しかし、複雑なため今回は使用しません。
select f.to_user_id, (
	CASE WHEN EXISTS(
			select f2.id
			from follows as f2
			where f2.from_user_id = f.to_user_id
			and f2.to_user_id = 1
		)
		THEN 'true'
		ELSE 'false'
	END
) is_follower
from follows as f
where f.from_user_id = 1