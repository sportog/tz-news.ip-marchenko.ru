<template>
    <div>
        <div
            v-for="category in categories"
            :key="category.id"
            class="category"
			:style="parseInt(category_id_parent) ? 'margin-left: 25px' : false"
			v-if="(parseInt(category_id_parent) && category_id_parent == category.category_id_parent) || (!parseInt(category_id_parent) && category.category_id_parent === null)"
        >
            <vs-button
                color="primary"
                type="border"
                icon="send"
                @click="apiCategorySub(category.id)"
                >{{ category.title }}</vs-button
            >
		<div>
			<categories
				:categories="categories"
				:category_id_parent="category.id"
				:apiCategorySub="apiCategorySub"
			></categories>
		</div>
	</div>
</template>

<script>
export default {
    name: "categories",
    props: ["categories", "category_id_parent", "apiCategorySub"],
};
</script>