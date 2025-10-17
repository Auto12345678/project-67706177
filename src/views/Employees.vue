<template>
  <div class="container mt-4">
    <h2 class="mb-3">รายการลูกจ้าง</h2>

    <div class="mb-3">
      <button class="btn btn-primary" @click="openAddModal">เพิ่มลูกจ้าง+</button>
    </div>

    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>ID</th>
          <th>ชื่อ</th>
          <th>นามสกุล</th>
          <th>ชื่อผู้ใช้งาน</th>
          <th>รูปภาพ</th>
          <th>การจัดการ</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="employee in employees" :key="employee.employee_id">
          <td>{{ employee.employee_id }}</td>
          <td>{{ employee.first_name }}</td>
          <td>{{ employee.last_name }}</td>
          <td>{{ employee.username }}</td>
          <td>
            <img
              v-if="employee.profile_picture"
              :src="'http://localhost/project-67706177/api_php/uploads/' + employee.profile_picture"
              width="100"
            />
          </td>
          <td>
            <button class="btn btn-warning btn-sm me-2" @click="openEditModal(employee)">
              แก้ไข
            </button>
            <button class="btn btn-danger btn-sm" @click="deleteEmployee(employee.employee_id)">
              ลบ
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="loading" class="text-center"><p>กำลังโหลดข้อมูล...</p></div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <!-- Modal ใช้ทั้งเพิ่ม / แก้ไข -->
    <div class="modal fade" id="editModal" tabindex="-1">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditMode ? "แก้ไขข้อมูลลูกจ้าง" : "เพิ่มลูกจ้างใหม่" }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveEmployee">
              <div class="mb-3">
                <label class="form-label">ชื่อ</label>
                <input v-model="editForm.first_name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">นามสกุล</label>
                <input v-model="editForm.last_name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">ชื่อผู้ใช้งาน</label>
                <input v-model="editForm.username" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">รหัสผ่าน</label>
                <input v-model="editForm.password" type="password" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">รูปภาพ</label>
                <!-- ✅ required เฉพาะตอนเพิ่มลูกจ้าง -->
                <input
                  type="file"
                  @change="handleFileUpload"
                  class="form-control"
                  :required="!isEditMode"
                />
                <!-- แสดงรูปเดิมเฉพาะตอนแก้ไข -->
                <div v-if="isEditMode && editForm.profile_picture">
                  <p class="mt-2">รูปเดิม:</p>
                  <img
                    :src="'http://localhost/project-67706177/api_php/uploads/' + editForm.profile_picture"
                    width="100"
                  />
                </div>
              </div>

              <button type="submit" class="btn btn-success">
                {{ isEditMode ? "บันทึกการแก้ไข" : "บันทึกลูกจ้างใหม่" }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";

export default {
  name: "EmployeeList",
  setup() {
    const employees = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const isEditMode = ref(false); // ✅ เช็คโหมด
    const editForm = ref({
      employee_id: null,
      first_name: "",
      last_name: "",
      username: "",
      password: "",
      profile_picture: ""
    });
    const newImageFile = ref(null);
    let modalInstance = null;

    // โหลดข้อมูลลูกจ้าง
    const fetchEmployees = async () => {
      try {
        const res = await fetch("http://localhost/project-67706177/api_php/api_employees.php");
        const data = await res.json();
        employees.value = data.success ? data.data : [];
      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

    // เปิด Modal สำหรับเพิ่มลูกจ้าง
    const openAddModal = () => {
      isEditMode.value = false;
      editForm.value = {
        employee_id: null,
        first_name: "",
        last_name: "",
        username: "",
        password: "",
        profile_picture: ""
      };
      newImageFile.value = null;

      const modalEl = document.getElementById("editModal");
      modalInstance = new window.bootstrap.Modal(modalEl);
      modalInstance.show();

      // ✅ รีเซ็ตค่า input file ให้ไม่แสดงชื่อไฟล์ค้าง
      const fileInput = modalEl.querySelector('input[type="file"]');
      if (fileInput) fileInput.value = "";
    };

    // เปิด Modal สำหรับแก้ไขข้อมูลลูกจ้าง
    const openEditModal = (employee) => {
      isEditMode.value = true;
      editForm.value = { ...employee };
      newImageFile.value = null;
      const modalEl = document.getElementById("editModal");
      modalInstance = new window.bootstrap.Modal(modalEl);
      modalInstance.show();
    };

    const handleFileUpload = (event) => {
      newImageFile.value = event.target.files[0];
    };

    // ✅ ใช้ฟังก์ชันเดียวในการเพิ่ม / แก้ไข
    const saveEmployee = async () => {
      const formData = new FormData();
      formData.append("action", isEditMode.value ? "update" : "add");
      if (isEditMode.value) formData.append("employee_id", editForm.value.employee_id);
      formData.append("first_name", editForm.value.first_name);
      formData.append("last_name", editForm.value.last_name);
      formData.append("username", editForm.value.username);
      formData.append("password", editForm.value.password);
      if (newImageFile.value) formData.append("profile_picture", newImageFile.value);

      try {
        const res = await fetch("http://localhost/project-67706177/api_php/api_employees.php", {
          method: "POST",
          body: formData
        });
        const result = await res.json();
        if (result.message) {
          alert(result.message);
          fetchEmployees();
          modalInstance.hide();
        } else if (result.error) {
          alert(result.error);
        }
      } catch (err) {
        alert(err.message);
      }
    };

    // ✅ ลบลูกค้า
// ✅ ลบลูกจ้าง
const deleteEmployee = async (id) => {
  if (!confirm("คุณต้องการลบข้อมูลนี้ใช่หรือไม่?")) return;

  const formData = new FormData();
  formData.append("action", "delete"); // กำหนด action เป็น delete
  formData.append("employee_id", id); // ส่ง employee_id ไป

  try {
    const response = await fetch("http://localhost/project-67706177/api_php/api_employees.php", {
      method: "POST", // ใช้ POST แทน DELETE
      body: formData
    });

    const result = await response.json();
    if (result.message) {
      employees.value = employees.value.filter(e => e.employee_id !== id); // ลบลูกจ้างจากรายการ
      alert(result.message);
    } else {
      alert(result.error); // แสดงข้อความถ้ามีข้อผิดพลาด
    }
  } catch (err) {
    alert("เกิดข้อผิดพลาด: " + err.message);
  }
};

    onMounted(fetchEmployees);

    return {
      employees,
      loading,
      error,
      editForm,
      isEditMode,
      openAddModal,
      openEditModal,
      handleFileUpload,
      saveEmployee,
      deleteEmployee
    };
  }
};
</script>
