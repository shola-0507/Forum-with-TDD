export default{

	data() {
		return {
			items: []
		}
	},

	methods: {
		add(Item) {
			this.items.push(Item);

			this.$emit('added');
		},

		remove(index) {
			this.items.splice(index, 1);

			this.$emit('removed');
		}
	}
}