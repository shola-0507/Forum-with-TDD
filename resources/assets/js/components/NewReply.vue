<template>
	<div>
		<div v-if="signedIn">
			<div class="form-group">
	            <textarea name="body" 
	            		  id="body" 
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
	
	import 'jquery.caret';
	import 'at.js';
	export default {

		data() {
			return {
				body: '',
				endpoint: location.pathname + '/replies',
			}
		},

		computed: {

			signedIn() {
				return window.App.signedIn;
			}
		},

		mounted() {

			$('#body').atwho({
				at: "@",
				delay: 750,
				callbacks: {

					remoteFilter: function(query, callback) {

						$.getJSON("/api/users", {name: query}, function(name) {
							callback(name)
						});
					}
				}
			});
		},

		methods: {

			addReply() {
				axios.post(this.endpoint, { body: this.body })

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