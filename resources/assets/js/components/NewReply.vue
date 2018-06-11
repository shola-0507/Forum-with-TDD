<template>
	<div>
		<div v-if="signedIn">
			<div class="form-group">
            <textarea name="body" 
            		  id="reply" 
            		  class="form-control" 
            		  placeholder="Wanna add something?"
            		  v-model="body" 
            		  rows="5">
            </textarea>
        </div>

        <button type="submit"
        		@click="addReply" 
        		class="btn btn-default">Post</button>
		</div>

        <p class="text-center" v-else>Please <a href="/login">sign in</a> to add a reply to this thread.</p>
	</div>
</template>

<script>
	
	import 'jquery-caret';
	import 'at.js';
	export default {

		data() {
			return {
				body: '',
			}
		},

		computed: {

			signedIn() {
				return window.App.signedIn;
			}
		},

		mounted() {

			// $('#reply').atwho({
			// 	at: "@",
			// 	delay: 750,
			// 	callbacks: {

			// 		remoteFilter: function(query, callback) {
			// 			console.log('called');
			// 		}

			// 		// $.getJSON("/users.php", {q = query}, function($usernames) {
			// 		// 	callback(usernames)
			// 		// });
			// 	}
			// });
		},

		methods: {

			addReply() {
				axios.post(location.pathname + '/replies', { body: this.body })

				.catch(error => {

					flash(error.response.data, 'danger');
				})

				.then(({data}) => {

					this.body = '';

					flash('Your reply has been posted.');

					this.$emit('created', data);
				});
			}
		}
	}
</script>