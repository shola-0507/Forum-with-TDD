<template>
	<div :id="'reply-'+id" class="panel panel-default" :class="isBest ? 'panel-success' : 'panel-default'">
	    <div class="panel-heading">
	     	<div class="level">
	     		<h5 class="flex">
	     			<a :href="'/profiles/'+data.owner.name" v-text="data.owner.name">
			     	</a> said <span v-text="ago"></span>
	     		</h5>
	     		
		     	<div v-if="authorize('updateReply', reply)"> 
		     		<favourite :reply="data"></favourite> 
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
	 	
		    <div class="panel-footer level">
		    	<div v-if="canUpdate">
		    		<button class="btn btn-xs mr-1" type="submit" @click="editing = true">Edit</button>
		    		<button class="btn btn-xs btn-danger mr-1" type="submit" @click="destroy">Delete</button>
		    	</div>
		    	
		    	<button class="btn btn-xs btn-primary ml-a" type="submit" @click="markBestReply" v-show="! isBest">Best Reply?</button>
		    </div>
	   
	</div>
</template>
<script>
	import Favourite from './Favourite.vue';
	import moment from 'moment';

	export default {

		props : ['data'],

		components : { Favourite },
		
		data() {
			return {
				editing : false,
				body : this.data.body,
				id: this.data.id,
				isBest: false,
				reply: this.data
			};
		},

		computed: {

			ago() {
				return moment(this.data.created_at).fromNow();
			}
		},

		methods: {
			
			update() {
				axios.patch('/replies/' + this.data.id, { body : this.body })

				.catch(error => { flash(error.response.data, 'danger') });

				this.editing = false;

				flash('Reply Updated!');
			},

			destroy() {
				axios.delete('/replies/' + this.data.id);

				this.$emit('deleted', this.data.id);
			},

			markBestReply() {
				this.isBest = true;
			}
		}
	}
</script>