var mix_after_login = {
  data: {
    tweet_box_text: '',
    min_new_tweet_id: null,
    delete_modal: {
      tweet_user_name: '',
      tweet_user_screen_name: '',
      tweet_text: '',
    },
    $will_delete_stream_item: null,
    will_delete_tweet_id: null, //削除予定のツイートID
    alert_message_text: '', //ツイートに関する各種操作の実行状況を表す
    searched_users: [],
    search_screen_name_kw: '',
  },
  methods: {
		redirectToProfile(e) {
			//dropmenu内のリンクが何故か動かないので作成。(data-toggleを付けていないことが原因だと思われるが調査断念。)
			$e_target = e.target;
			window.location.href = $e_target.href;
		},
    inputedKeywordsSearch(e) {
      //ユーザ検索(入力された検索スクリーン名キーワードによって)
      $e_target = e.target;
      const inputed_val = $e_target.value;
			//入力値が正しい場合のみ検索結果を表示。
      if (inputed_val[0] === '@' && inputed_val.length > 1) {
        const params = new URLSearchParams();
        //検索されたスクリーンネームを取得(検索値から先頭の@を取り除く)。
        this.search_screen_name_kw = inputed_val.substr(1);
        params.append('search_screen_name_kw', this.search_screen_name_kw);
        axios.post('/twitter/public/ajax_q/get_searched_users', params)
          .then((response) => {
            console.log(response.data);
            //ユーザ流し込み
            this.searched_users = response.data.users;
            if (this.searched_users.length > 0) {
              $('#js-keywordsSearch-dropdown').dropdown('show');
            }
          })
          .catch(function(error) {
            console.log(error);
          });
      } else {
        $('#js-keywordsSearch-dropdown').dropdown('hide');
      }
    },
    showTweetModal() {
      $('#tweetModal').modal('show')
    },
    insertNewTweet() {
      $('#tweetModal').modal('hide');
      const params = new URLSearchParams();
      const $tweetModalTextarea = document.getElementById('tweetModalTextarea');
      params.append('tweet_text', $tweetModalTextarea.value);
      this.alert_message_text = 'ツイートを送信中。';
      axios.post('/twitter/public/ajax_q/insert_new_tweet', params)
        .then((response) => {
          console.log(response.data);
          this.new_tweets_cnt += 1;
          const new_tweet_id = response.data.tweet_id;
          if (this.min_new_tweet_id === null || this.min_new_tweet_id > new_tweet_id) {
            this.min_new_tweet_id = new_tweet_id;
          }
          this.alert_message_text = 'ツイートを送信しました。';
          this._showTweetMessageDrawer();
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    showTweetDeleteModal(e) {
      $e_target = e.target;
      //削除対象のツイートDOMを削除まで一時保存。
      this.$will_delete_stream_item = $e_target.closest('.js-stream-item');
      $tweet_content = $e_target.closest('.js-tweet-content');
      //削除対象のツイートIDを削除まで一時保存。
      this.will_delete_tweet_id = $tweet_content.dataset.tweetId;
      //削除対象のツイートIDをモーダルに表示する作業。
      $tweet_user_name = $tweet_content.getElementsByClassName('jsTweetUserName')[0].innerHTML;
      $tweet_user_screen_name = $tweet_content.getElementsByClassName('jsTweetUserScreenName')[0].innerHTML;
      $tweet_text = $tweet_content.getElementsByClassName('jsTweetText')[0].innerHTML;
      this.delete_modal.tweet_user_name = $tweet_user_name;
      this.delete_modal.tweet_user_screen_name = $tweet_user_screen_name;
      this.delete_modal.tweet_text = $tweet_text;
      $('#delete-tweet-dialog').modal('show')
    },
    deleteTweet() {
      const params = new URLSearchParams();
      params.append('will_delete_tweet_id', this.will_delete_tweet_id);
      axios.post('/twitter/public/ajax_q/delete_tweet', params)
        .then((response) => {
          // console.log(response.data)
          $('#delete-tweet-dialog').modal('hide')
          this.$will_delete_stream_item.remove();
          this.alert_message_text = 'ツイートが削除されました。';
          this._showTweetMessageDrawer();
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    //ツイートに関する各種操作の実行結果などをページトップに表示します。
    _showTweetMessageDrawer() {
      setTimeout(() => {
        this.$refs.messageDrawer.style.top = '0px';
      }, 1);
      setTimeout(() => {
        this.$refs.messageDrawer.style.top = '-63px';
      }, 3000);
    },
  }
};

$(document).click(function(e) {
  $e_target = e.target;
  //search-inputなら検索されたユーザー表示ボックスを消さない。
  if ($e_target.classList.contains('search-input')) return;
  $('#js-keywordsSearch-dropdown').dropdown('hide');
});
