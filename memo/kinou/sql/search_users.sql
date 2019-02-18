-- ログインユーザを1とし、検索スクリーン名キーワードを'sc'とした場合、そのユーザ情報と、
-- ログインユーザから、そのユーザに対してのフォロー状況も取得。

select u.id as user_id, u.name as u_name, u.screen_name as u_screen_name, p.text as p_text, (
	select 1
	from follows as f2
	where f2.to_user_id = u.id
	and f2.from_user_id = 1
) is_auth_following_ifnotnull, (
	select 1
	from follows as f3
	where f3.from_user_id = u.id
	and f3.to_user_id = 1
) as is_auth_follower_ifnotnull
from users as u
join profilese as p on u.id = p.user_id
and u.screen_name like '%sc%'