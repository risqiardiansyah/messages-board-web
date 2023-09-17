<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';

let lastData = ref(false)
let paginate = ref({
    limit: 16,
    offset: 0,
    search: ''
})
let dataPesan = ref([])
let dataInserted = ref({
    id: "",
    nama: "",
    email: "",
    isi: "",
});
let errorForm = ref({
    nama: "",
    email: "",
    isi: "",
});

onMounted(async () => {
    getData()

    const masonry = document.querySelector('#infinte-scroll');
    masonry.addEventListener('scroll', e => {
        console.log(masonry.scrollTop + masonry.clientHeight);
        console.log(masonry.scrollHeight - 1);
        console.log(masonry.scrollTop + masonry.clientHeight >= masonry.scrollHeight - 1);
        console.log(!lastData);

        if (masonry.scrollTop + masonry.clientHeight >= masonry.scrollHeight - 1 && dataPesan.value.length >= paginate.value.offset) {
            paginate.value.offset = paginate.value.offset + paginate.value.limit
            getData()
        }
    })
})

const getData = async (refresh = false) => {
    await axios.get(`/api/v1/pesan?${new URLSearchParams(paginate.value)}`)
        .then(res => {
            if (refresh) {
                dataPesan.value = [];
            }

            const data = res.data.data

            if (data.length <= 0 || data.length < paginate.value.limit) {
                lastData.value = true
            } else {
                lastData.value = false
            }

            data.forEach(element => {
                dataPesan.value.push(element)
            });
        })
}

const savePesan = async () => {
    await axios.post("/api/v1/pesan/create", dataInserted.value)
        .then(res => {
            paginate.value.offset = 0

            toastr.success("Pesan Berhasil disimpan!");
            closeModal('closeModalTambah')
            getData(true)
            dataInserted.value = {
                id: "",
                nama: "",
                email: "",
                isi: "",
            }
            errorForm.value = {
                id: "",
                nama: "",
                email: "",
                isi: "",
            }
        })
        .catch(err => {
            errorForm.value = err.response.data.data
            toastr.error("Pesan Gagal disimpan!");
        })
}

const editPesan = async () => {
    await axios.post("/api/v1/pesan/update", dataInserted.value)
        .then(res => {
            paginate.value.offset = 0

            toastr.success("Pesan Berhasil diedit!");
            getData(true)
            closeModal('closeModalEdit')
            dataInserted.value = {
                id: "",
                nama: "",
                email: "",
                isi: "",
            }
            errorForm.value = {
                id: "",
                nama: "",
                email: "",
                isi: "",
            }
        })
        .catch(err => {
            errorForm.value = err.response.data.data
            toastr.error("Pesan Gagal disimpan!");
        })
}

const deletePesan = async (item) => {
    await axios.delete(`/api/v1/pesan/delete/${item.id}`)
        .then(res => {
            toastr.success("Pesan Berhasil dihapus!");

            const newDataPesan = dataPesan.value.filter(x => x.id != item.id)
            dataPesan.value = newDataPesan
        })
        .catch(err => {
            toastr.error("Pesan Gagal disimpan!");
        })
}

const handleSearch = (item) => {
    paginate.value.limit = 16
    paginate.value.offset = 0
    getData(true)
}

const setEditData = (data) => {
    dataInserted.value = {
        id: data.id,
        nama: data.nama,
        email: data.email,
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

const handleChangeSearch = (e) => {
    paginate.value[e.target.name] = e.target.value
}

</script>
<template>
    <div class="container mt-5">
        <div class="mailbox-container pb-5">
            <div class="mailbox-header text-center">
                <h1>Message Board</h1>
            </div>
            <div class="row p-3 search-input d-flex justify-content-between">
                <div class="col-md-4 d-flex">
                    <input type="text" class="form-control me-2" name="search" placeholder="Cari pesan..."
                        v-model="paginate.search" v-on:input="handleChangeSearch">
                    <button type="button" class="btn btn-primary" v-on:click="handleSearch">Cari</button>
                </div>

                <div class="col-md-8 d-flex justify-content-end">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#modalTambah">Tambah Pesan</button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="message-list" id="infinte-scroll">
                        <div class="container">
                            <div v-if="dataPesan.length">
                                <div class="row gx-2">
                                    <div class="col-md-3 col-sm-12" v-for="item in dataPesan" :key="item.id">
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between">
                                                <h5 class="m-0">
                                                    {{ item.nama }}
                                                    <small class="text-muted">
                                                        {{ item.email }}
                                                    </small>
                                                </h5>

                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false"></button>
                                                    <ul class="dropdown-menu">
                                                        <li><a href="#" class="dropdown-item" data-bs-toggle="modal"
                                                                data-bs-target="#modalEdit"
                                                                v-on:click="setEditData(item)">Edit</a></li>
                                                        <li><a href="#" class="dropdown-item"
                                                                v-on:click="deletePesan(item)">Hapus</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <p>{{ item.isi }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="lastData" class="text-center">Tidak ada data lagi</div>
                            </div>
                            <div class="list-group text-center p-3" v-else>
                                <p>Belum Ada Pesan</p>
                            </div>
                        </div>
                    </div>
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
                                <small class="text-danger">{{ errorForm.nama }}</small>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    v-model="dataInserted.email" v-on:input="handleChange">
                                <small class="text-danger">{{ errorForm.email }}</small>
                            </div>
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Isi Pesan" id="floatingTextarea2" name="isi"
                                    style="height: 100px" v-model="dataInserted.isi" v-on:input="handleChange"></textarea>
                                <label for="floatingTextarea2">Pesan</label>
                                <small class="text-danger">{{ errorForm.isi }}</small>
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
                                <small class="text-danger">{{ errorForm.nama }}</small>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    v-model="dataInserted.email" v-on:input="handleChange">
                                <small class="text-danger">{{ errorForm.email }}</small>
                            </div>
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Isi Pesan" id="floatingTextarea2" name="isi"
                                    style="height: 100px" v-model="dataInserted.isi" v-on:input="handleChange"></textarea>
                                <label for="floatingTextarea2">Pesan</label>
                                <small class="text-danger">{{ errorForm.isi }}</small>
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