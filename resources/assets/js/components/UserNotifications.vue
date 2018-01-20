<template>
	
	<li class="dropdown" v-if="notifications.length">

		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			<span class="glyphicon glyphicon-bell"></span> <span class="caret"></span>
		</a>

		<ul class="dropdown-menu">

			<li v-for="notification in notifications">

				<a :href="notification.data.link" v-text="notification.data.message" @click="MarkAsRead(notification)"></a>

			</li>

		</ul>

	</li>

</template>

<script>
	
	export default {

		data() {

			return {

				notifications: false,
			}
		},

		created() {

			axios.get("/profiles/" + window.App.user.name + "/notifications")

				.then(response => this.notifications = response.data);
		},

		methods: {

			MarkAsRead(notification) {

				axios.delete("/profiles/" + window.App.user.name + "/notifications/" + notification.id)
			}
		}
	}

</script>