<template>
	<div :id="'reply-'+id" class="panel panel-default" :class="isBest ? 'panel-success' : 'panel-default'">
	    <div class="panel-heading">
	     	<div class="level">
	     		<h5 class="flex">
	     			<a :href="'/profiles/'+reply.owner.name" v-text="reply.owner.name">
			     	</a> said <span v-text="ago"></span>
	     		</h5>
	     		
		     	<div v-if="signedIn"> 
		     		<favourite :reply="reply"></favourite> 
		     	</div>
			   
	     	</div>
	    </div>

	    <div class="panel-body">
	    	<div v-if="editing">
	    		<form @submit="update">
		    		<div class="form-group">
		    			<textarea class="form-control" v-model="body" required></textarea>
		    		</div>
		    		
		    		<button class="btn btn-primary btn-xs">Update</button>
		    		<button class="btn btn-link btn-xs" @click="editing = false" type="button">Cancel</button>
	    		</form>
	    	</div>
	    	<div v-else v-html="body"></div>
	    </div>
	 	
	    <div class="panel-footer level" v-if="authorize('owns', reply) || authorize('owns', reply.thread)">
	    	<div v-if="authorize('owns', reply)">
	    		<button class="btn btn-xs mr-1" type="submit" @click="editing = true">Edit</button>
	    		<button class="btn btn-xs btn-danger mr-1" type="submit" @click="destroy">Delete</button>
	    	</div>
	    	
	    	<button class="btn btn-xs btn-primary ml-a" type="submit" @click="markBestReply" v-if="authorize('owns', reply.thread)">Best Reply?</button>
	    </div>
	   
	</div>
</template>
<script>
	import Favourite from './Favourite.vue';
	import moment from 'moment';

	export default {

		props : ['reply'],

		components : { Favourite },
		
		data() {
			return {
				editing : false,
				body : this.reply.body,
				id: this.reply.id,
				isBest: this.reply.isBest,
				reply: this.reply
			};
		},

		computed: {

			ago() {
				return moment(this.reply.created_at).fromNow();
			}
		},

		created() {
			window.events.$on('best-reply-selected', id => {
				this.isBest = (id === this.id);
			})
		},

		methods: {
			
			update() {
				axios.patch('/replies/' + this.reply.id, { body : this.body })

				.catch(error => { flash(error.response.data, 'danger') });

				this.editing = false;

				flash('Reply Updated!');
			},

			destroy() {
				axios.delete('/replies/' + this.reply.id);

				this.$emit('deleted', this.reply.id);
			},

			markBestReply() {
				axios.post('/replies/' + this.reply.id + '/best');

				window.events.$emit('best-reply-selected', this.reply.id);
			}
		}
	}
</script>