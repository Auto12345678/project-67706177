<template>
  <div class="container mt-4 col-md-4 bg-body-secondary ">
    <h2 class="text-center mb-3">เพิ่มนักเรียน</h2>
    <form @submit.prevent="addProduct">
      <div class="mb-2">
        <input v-model="product.frist_name" class="form-control" placeholder="ชื่อ" required />
      </div>
      <div class="mb-2">
        <input v-model="product.last_name" class="form-control" placeholder="นามสกุล" required />
      </div>
      <div class="mb-2">
        <input  v-model="product.email" class="form-control" placeholder="อีเมล" required />
      </div>
      <div class="mb-2">
        <input v-model="product.phone" class="form-control" placeholder="เบอร์โทร" required />
      </div>
      
      <div class="text-center mt-4 ">
      <button type="submit" class="btn btn-primary mb-4">บันทึก</button> &nbsp;
      <button type="reset" class="btn btn-secondary mb-4">ยกเลิก</button>
      </div>
    </form>

    <div v-if="message" class="alert alert-info mt-3">
      {{ message }}
    </div>
  </div>
</template>


<script>
export default {
  data() {
    return {
      product: {
        frist_name: "",
        last_name: "",
        email: "",
        phone: "",
        
      },
      message: "",
    };
  },
  methods: {
    onFileChange(e) {
      this.product.image = e.target.files[0];
    },
    async addProduct() {
      try {
        // ใช้ FormData สำหรับส่ง text + file
        const formData = new FormData();
        formData.append("frist_name", this.product.frist_name);
        formData.append("last_name", this.product.last_name);
        formData.append("email", this.product.email);
        formData.append("phone", this.product.phone);
        

        const res = await fetch("http://localhost/project-67706177/api_php/add_student.php", {
          method: "POST",
          body: formData, // ❌ ห้ามใส่ Content-Type เดี๋ยว browser จะจัดการเอง
        });

        const data = await res.json();
        this.message = data.message;

        if (data.success) {
          // ✅ เคลียร์ข้อมูล
          this.product = { frist_name: "", last_name: "", email: "", phone: "" };
          this.$refs.fileInput.value = "";
        }
      } catch (err) {
        this.message = "เกิดข้อผิดพลาด: " + err.message;
      }
    },
  },
};
</script>