var app = new Vue({
  el: '#app',
  mixins: [mix_after_login],
  data: {
    new_tweets_cnt: 0,
		new_tweets: [],
  },
  methods: {
    showNewTweets() {
			this.new_tweets_cnt = 0;
      const params = new URLSearchParams();
      params.append('min_new_tweet_id', this.min_new_tweet_id);
      axios.post('/twitter/public/ajax_q/get_new_tweets_by_min_id', params)
        .then((response) => {
          console.log(response.data);
					const new_tweets = response.data.tweets;
					this.new_tweets = new_tweets.concat(this.new_tweets);
        })
        .catch(function(error) {
          console.log(error);
        });
    },
		toggleToTweetBoxCondensed() {
			this.$refs.tweetForm.classList.add('condensed');
		},
		toggleToTweetBoxNoCondensed(e) {
			const $e_target = e.target;
			this.$refs.tweetForm.classList.remove('condensed');
			$('#jsTweetBox').focus();
		},
  }
});

//condensed移行時にクリックアクションを付け外ししたほうが無駄がなくていいかなと思う。
const $jsTweetForm = document.getElementById('jsTweetForm');
$(document).click(function(e) {
	if($jsTweetForm.classList.contains('condensed') === true) return;
	$e_target = e.target;
	if($e_target.classList.contains('tmp-tweet-box') === true) return;
	if($e_target.classList.contains('tweet-box') === false && $e_target.classList.contains('js-tweet-btn') === false) {
		app.toggleToTweetBoxCondensed();
	}
});