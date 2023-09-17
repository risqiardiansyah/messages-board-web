<script setup>
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';

let loading = ref(false)
let paginate = ref({
    limit: 4,
    offset: 0,
})
let dataPesan = ref([])
let showPesan = ref({
    show: false,
    data: {}
});
let dataInserted = ref({
    id: "",
    nama: "",
    email: "",
    isi: "",
});

onMounted(async () => {
    loading.value = true
    getData()

    const masonry = document.querySelector('#infinte-scroll');
    masonry.addEventListener('scroll', e => {

        if (masonry.scrollTop + masonry.clientHeight >= masonry.scrollHeight - 10) {
            paginate.value.offset = paginate.value.offset + paginate.value.limit
            getData()
        }
    })
})

const getData = async () => {
    await axios.get(`/api/v1/pesan?${new URLSearchParams(paginate.value)}`)
        .then(res => {
            loading.value = false

            dataPesan.value = res.data.data
        })
        .catch(err => {
            loading.value = false
        });
}

const savePesan = async () => {
    await axios.post("/api/v1/pesan/create", dataInserted.value)
        .then(res => {
            toastr.success("Pesan Berhasil disimpan!");
            getData()
            closeModal('closeModalTambah')
            dataInserted.value = {
                id: "",
                nama: "",
                email: "",
                isi: "",
            }
        })
        .catch(err => {
            toastr.success("Pesan Gagal disimpan!");
        })
}

const editPesan = async () => {
    await axios.post("/api/v1/pesan/update", dataInserted.value)
        .then(res => {
            toastr.success("Pesan Berhasil diedit!");
            getData()
            closeModal('closeModalEdit')
            dataInserted.value = {
                id: "",
                nama: "",
                email: "",
                isi: "",
            }
            const data = res.data.data
            showPesan.value.data = data
        })
        .catch(err => {
            toastr.success("Pesan Gagal disimpan!");
        })
}

const deletePesan = async (item) => {
    await axios.delete(`/api/v1/pesan/delete/${item.id}`)
        .then(res => {
            toastr.success("Pesan Berhasil diedit!");
            getData()
            closeModal('closeModalEdit')
            dataInserted.value = {
                id: "",
                nama: "",
                email: "",
                isi: "",
            }
            const data = res.data.data
            showPesan.value.data = data
        })
        .catch(err => {
            toastr.success("Pesan Gagal disimpan!");
        })
}

const setShowPesan = (item) => {
    showPesan.value.show = true
    showPesan.value.data = item
}

const setEditData = () => {
    const data = showPesan.value.data
    dataInserted.value = {
        id: data.id,
        nama: data.user.nama,
        email: data.user.email,
        isi: data.isi,
    }
}

const resetDataInserted = () => {
    dataInserted.value = {
        id: "",
        nama: "",
        email: "",
        isi: "",
    }
}

const closeModal = (id) => {
    document.getElementById(id).click();
}

const handleChange = (e) => {
    dataInserted.value[e.target.name] = e.target.value
}

</script>
<template>
    <div class="container mt-5">
        <div class="mailbox-container pb-5">
            <div class="mailbox-header text-center">
                <h1>Message Board</h1>
            </div>
            <div class="row p-3 search-input d-flex justify-content-between">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Cari pesan...">
                </div>

                <div class="col-md-8 d-flex justify-content-end">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#modalTambah">Tambah Pesan</button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 message-list" id="infinte-scroll">
                    <div class="list-group" v-if="dataPesan.length">
                        <a href="#" class="list-group-item list-group-item-action py-3 d-flex justify-content-between"
                            :class="{ active: item.id == showPesan.data.id }" v-for="item in dataPesan" :key="item.id"
                            v-on:click="setShowPesan(item)">
                            <div>
                                {{ item.user.nama }}
                                <small class="text-muted">
                                    {{ item.user.email }}
                                </small>
                            </div>
                        </a>
                        <div v-if="loading" class="loading">Loading...</div>
                    </div>
                    <div class="list-group" v-else>
                        <p>Belum Ada Pesan</p>
                    </div>
                </div>

                <!-- Preview Pesan -->
                <div class="col-md-8 message-preview" v-if="showPesan.show">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="m-0">
                                {{ showPesan.data.user.nama }}
                                <small class="text-muted">
                                    {{ showPesan.data.user.email }}
                                </small>
                            </h5>
                            <p class="m-0">
                                {{ showPesan.data.created_at }}
                            </p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-10">
                                    <p>{{ showPesan.data.isi }}</p>
                                </div>
                                <div class="col-md-2 d-flex flex-column">
                                    <button type="button" class="btn btn-warning max-height-40" data-bs-toggle="modal"
                                        data-bs-target="#modalEdit" v-on:click="setEditData">Edit</button>
                                    <button type="button" class="btn btn-danger max-height-40"
                                        v-on:click="deletePesan(item)">Hapus</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8" v-else>
                    <p>Silahkan Pilih Pesan</p>
                </div>
            </div>
        </div>

        <!-- Modal Tambah -->
        <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalTambahLabel">Tambah Pesan</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            v-on:click="resetDataInserted"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" v-model="dataInserted.nama"
                                    v-on:input="handleChange">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    v-model="dataInserted.email" v-on:input="handleChange">
                            </div>
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Isi Pesan" id="floatingTextarea2" name="isi"
                                    style="height: 100px" v-model="dataInserted.isi" v-on:input="handleChange"></textarea>
                                <label for="floatingTextarea2">Pesan</label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeModalTambah"
                            v-on:click="resetDataInserted">Close</button>
                        <button type="button" class="btn btn-primary" v-on:click="savePesan">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal Tambah -->

        <!-- Modal Tambah -->
        <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalEditLabel">Edit Pesan</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            v-on:click="resetDataInserted"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" v-model="dataInserted.nama"
                                    v-on:input="handleChange">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    v-model="dataInserted.email" v-on:input="handleChange">
                            </div>
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Isi Pesan" id="floatingTextarea2" name="isi"
                                    style="height: 100px" v-model="dataInserted.isi" v-on:input="handleChange"></textarea>
                                <label for="floatingTextarea2">Pesan</label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeModalEdit"
                            v-on:click="resetDataInserted">Close</button>
                        <button type="button" class="btn btn-primary" v-on:click="editPesan">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal Tambah -->
    </div>
</template>