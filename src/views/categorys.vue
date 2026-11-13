
<template>
  <div class="container mt-4">
    <h2 class="mb-3">รายชื่อประเภทสินค้า</h2>

    <div class="mb-3">
      <button class="btn btn-primary" @click="openAddModal">
        เพิ่มประเภทสินค้า <i class="bi bi-plus-circle"></i>
      </button>
    </div>

    <div v-if="loading" class="text-center">
      <p>กำลังโหลดข้อมูล...</p>
    </div>

    <div v-if="error" class="alert alert-danger">
      {{ error }}
    </div>

    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>ID</th>
          <th>ประเภทสินค้า</th>
          <th>แก้ไข-ลบ</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="category in categorys" :key="category.category_id">
          <td>{{ category.category_id }}</td>
          <td>{{ category.category_name }}</td>
          <td>
            <button
              class="btn btn-warning btn-sm"
              @click="openEditModal(category)"
            >
              แก้ไข
            </button>

            |

            <button
              class="btn btn-danger btn-sm"
              @click="deleteCategory(category.category_id)"
            >
              ลบ
            </button>
          </td>
        </tr>

        <tr v-if="categorys.length === 0 && !loading">
          <td colspan="3" class="text-center">
            ไม่พบข้อมูลประเภทสินค้า
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal เพิ่ม / แก้ไข -->
    <div class="modal fade" id="editModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title">
              {{ isEditMode ? "แก้ไขประเภทสินค้า" : "เพิ่มประเภทสินค้า" }}
            </h5>

            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
            ></button>
          </div>

          <div class="modal-body">
            <form @submit.prevent="saveCategory">

              <div class="mb-3">
                <label class="form-label">ชื่อประเภทสินค้า</label>

                <input
                  v-model="editCategory.category_name"
                  type="text"
                  class="form-control"
                  placeholder="กรอกชื่อประเภทสินค้า"
                  required
                >
              </div>

              <button type="submit" class="btn btn-success">
                {{ isEditMode ? "บันทึกการแก้ไข" : "เพิ่มประเภทสินค้า" }}
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
  name: "CategoryList",

  setup() {
    const categorys = ref([]);
    const loading = ref(true);
    const error = ref(null);

    const editCategory = ref({
      category_name: ""
    });

    const isEditMode = ref(false);

    let editModal = null;

    const API_URL =
      "http://localhost/MK_Shop/php_api/api_categorys.php";

    // ดึงข้อมูลประเภทสินค้า
    const fetchCategories = async () => {
      loading.value = true;
      error.value = null;

      try {
        const response = await fetch(API_URL);
        const result = await response.json();

        if (result.success) {
          categorys.value = result.data;
        } else {
          error.value = result.message || "ไม่สามารถดึงข้อมูลได้";
        }

      } catch (err) {
        error.value = err.message;

      } finally {
        loading.value = false;
      }
    };

    onMounted(() => {
      fetchCategories();

      const modalEl = document.getElementById("editModal");

      editModal = new window.bootstrap.Modal(modalEl);
    });

    // เปิด Modal เพิ่มประเภทสินค้า
    const openAddModal = () => {
      isEditMode.value = false;

      editCategory.value = {
        category_name: ""
      };

      editModal.show();
    };

    // เปิด Modal แก้ไขประเภทสินค้า
    const openEditModal = (category) => {
      isEditMode.value = true;

      editCategory.value = {
        ...category
      };

      editModal.show();
    };

    // เพิ่ม / แก้ไขประเภทสินค้า
    const saveCategory = async () => {
      const formData = new FormData();

      formData.append(
        "action",
        isEditMode.value ? "update" : "add"
      );

      formData.append(
        "category_name",
        editCategory.value.category_name
      );

      if (isEditMode.value) {
        formData.append(
          "category_id",
          editCategory.value.category_id
        );
      }

      try {
        const response = await fetch(API_URL, {
          method: "POST",
          body: formData
        });

        const result = await response.json();

        if (result.success) {
          alert(result.message);

          await fetchCategories();

          editModal.hide();

        } else {
          alert(result.message || result.error);
        }

      } catch (err) {
        alert("เกิดข้อผิดพลาด: " + err.message);
      }
    };

    // ลบประเภทสินค้า
    const deleteCategory = async (id) => {
      if (!confirm("คุณต้องการลบประเภทสินค้านี้ใช่หรือไม่?")) {
        return;
      }

      const formData = new FormData();

      formData.append("action", "delete");
      formData.append("category_id", id);

      try {
        const response = await fetch(API_URL, {
          method: "POST",
          body: formData
        });

        const result = await response.json();

        if (result.success) {
          alert(result.message);

          await fetchCategories();

        } else {
          alert(result.message || result.error);
        }

      } catch (err) {
        alert("เกิดข้อผิดพลาด: " + err.message);
      }
    };

    return {
      categorys,
      loading,
      error,
      editCategory,
      isEditMode,
      openAddModal,
      openEditModal,
      saveCategory,
      deleteCategory
    };
  }
};
</script>
