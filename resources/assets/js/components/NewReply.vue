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