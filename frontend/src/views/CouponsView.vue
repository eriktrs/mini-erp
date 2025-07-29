<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Coupons</h2>
    <CouponForm :formData="couponForm" :editMode="isEditing" @submit="handleSubmit" @cancel="cancelEdit" />
    <CouponTable :coupons="coupons" @edit="editCoupon" @delete="deleteCoupon" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import CouponForm from "../components/coupons/CouponForm.vue";
import CouponTable from "../components/coupons/CouponTable.vue";
import { useCoupons } from "../composables/useCoupons";

const { coupons, fetchCoupons, createCoupon, updateCoupon, removeCoupon } = useCoupons();
const isEditing = ref(false);
const couponForm = ref({ id: null, code: "", discount: 0, min_value: 0, valid_until: "" });

onMounted(fetchCoupons);

async function handleSubmit() {
  if (isEditing.value && couponForm.value.id) {
    await updateCoupon(couponForm.value.id, couponForm.value);
  } else {
    await createCoupon(couponForm.value);
  }
  resetForm();
}

function editCoupon(coupon: any) {
  couponForm.value = { ...coupon };
  isEditing.value = true;
}

async function deleteCoupon(id: number) {
  await removeCoupon(id);
}

function cancelEdit() {
  resetForm();
}

function resetForm() {
  couponForm.value = { id: null, code: "", discount: 0, min_value: 0, valid_until: "" };
  isEditing.value = false;
}
</script>
