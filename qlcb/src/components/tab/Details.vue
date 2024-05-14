<template>
    <div class="content">
        <div class="grid-container">
            <div class="grid-item">
                <h2>THÔNG TIN CƠ QUAN</h2>
            </div>

            <div class="grid-item">
                <h2>THÔNG TIN CÁ NHÂN</h2>
            </div>

            <div class="grid-item">
                <NcButton v-if="!this.edit" class="edit-button" aria-label="Edit" type="secondary"
                    @click="startEditting">
                    <template #icon>
                        <Pencil :size="20" />
                    </template>
                </NcButton>
                <div v-else class="combo-buttons">
                    <NcButton aria-label="Edit" type="secondary" @click="cancelEditting">
                        <template #icon>
                            <Close :size="20" />
                        </template>
                    </NcButton>
                    <NcButton aria-label="Edit" type="primary" @click="submitEditting">
                        <template #icon>
                            <Check :size="20" />
                        </template>
                    </NcButton>
                </div>
            </div>

            <div class="grid-item">
                <label>Tên người dùng (*)</label>
                <input class="input-disabled" type="text" v-model="user.qlcb_uid" :disabled="true" />
                <div class="validation-error-container">
                </div>
            </div>

            <div class="grid-item">
                <label>Họ và tên (*)</label>
                <input v-if="!edit" class="input-disabled" type="text" v-model="user.full_name" :disabled="true" />
                <input v-else id="full_name" type="text" v-model="user.full_name" />
                <div class="validation-error-container">
                    <span class="validation-error" v-if="!validation.requiredString(user.full_name)">
                        {{ validationMessages['required'] }}
                    </span>
                    <span class="validation-error" v-if="!validation.fullName(user.full_name)">
                        {{ validationMessages['full_name'] }}
                    </span>
                </div>
            </div>

            <div class="grid-item">
                <label>Ngày sinh (*)</label>
                <input v-if="!edit" class="input-disabled" type="text" v-model="getDateOfBirth" :disabled="true" />
                <NcDatetimePicker v-else format="DD/MM/YYYY" class="nc-picker" id="date_of_birth"
                    v-model="dateOfBirth" />
                <div class="validation-error-container">
                    <span class="validation-error" v-if="!validation.requiredObject(dateOfBirth)">
                        {{ validationMessages['required'] }}
                    </span>
                </div>
            </div>

            <div class="grid-item">
                <label>Chức vụ (*)</label>
                <input v-if="!edit" class="input-disabled" type="text" v-model="getPositionName" :disabled="true" />
                <Multiselect v-else id="position_id" v-model="user.position_id" :options-list="formatPositions"
                    :id="user.position_id" :allow-delete="false" type="position" />
                <div class="validation-error-container">
                </div>
            </div>

            <div class="grid-item">
                <label>Giới tính (*)</label>
                <input v-if="!edit" class="input-disabled" type="text" v-model="getGender" :disabled="true" />
                <NcMultiselect v-else class="nc-select" id="gender" v-model="user.gender" :options="genders"
                    label="text" track-by="value" />
                <div class="validation-error-container">
                </div>
            </div>

            <div class="grid-item">
                <label>Số điện thoại (*)</label>
                <input v-if="!edit" class="input-disabled" type="text" v-model="user.phone" :disabled="true" />
                <input v-else id="phone" type="text" v-model="user.phone" />
                <div class="validation-error-container">
                    <span class="validation-error" v-if="!validation.requiredString(user.phone)">
                        {{ validationMessages['required'] }}
                    </span>
                    <span class="validation-error" v-if="!validation.phone(user.phone)">
                        {{ validationMessages['phone'] }}
                    </span>
                </div>
            </div>

            <div class="grid-item">
                <label>Đơn vị (*)</label>
                <input v-if="!edit" class="input-disabled" type="text" v-model="getUnitName" :disabled="true" />
                <Multiselect v-else id="unit_id" :options-list="formatUnits" type="unit" v-model="user.unit_id"
                    :id="user.unit_id" :allow-delete="false" />
                <div class="validation-error-container">
                </div>
            </div>

            <div class="grid-item">
                <label>Email (*)</label>
                <input v-if="!edit" class="input-disabled" type="text" v-model="user.email" :disabled="true" />
                <input v-else id="email" type="text" v-model="user.email" />
                <div class="validation-error-container">
                    <span class="validation-error" v-if="!validation.requiredString(user.email)">
                        {{ validationMessages['required'] }}
                    </span>
                    <span class="validation-error" v-if="!validation.email(user.email)">
                        {{ validationMessages['email'] }}
                    </span>
                </div>
            </div>

            <div class="grid-item">
                <label>CCCD/CMND (*)</label>
                <input v-if="!edit" class="input-disabled" type="text" v-model="user.id_number" :disabled="true" />
                <input v-else id="id_number" type="text" v-model="user.id_number" />
                <div class="validation-error-container">
                    <span class="validation-error" v-if="!validation.requiredString(user.id_number)">
                        {{ validationMessages['required'] }}
                    </span>
                    <span class="validation-error" v-if="!validation.idNumber(user.id_number)">
                        {{ validationMessages['id_number'] }}
                    </span>
                </div>
            </div>

            <div class="grid-item">
                <label>Hệ số lương (*)</label>
                <input v-if="!edit" class="input-disabled" type="text" v-model="user.coefficients_salary"
                    :disabled="true" />
                <input v-else id="coefficients_salary" type="text" v-model="user.coefficients_salary" />
                <div class="validation-error-container">
                    <span class="validation-error" v-if="!validation.requiredString(user.coefficients_salary)">
                        {{ validationMessages['required'] }}
                    </span>
                    <span class="validation-error" v-if="!validation.decimal(user.coefficients_salary)">
                        {{ validationMessages['decimal'] }}
                    </span>
                </div>
            </div>

            <div class="grid-item">
                <label>Địa chỉ (*)</label>
                <input v-if="!edit" class="input-disabled" type="text" v-model="user.address" :disabled="true" />
                <input v-else id="address" type="text" v-model="user.address" />
                <div class="validation-error-container">
                    <span class="validation-error" v-if="!validation.requiredString(user.address)">
                        {{ validationMessages['required'] }}
                    </span>
                </div>
            </div>


            <div class="grid-item">
                <label>Ngày vào Đảng</label>
                <input v-if="!edit" class="input-disabled" type="text" v-model="getCommunity" :disabled="true" />
                <NcDatetimePicker v-else format="DD/MM/YYYY" class="nc-picker" id="communist_party_joined"
                    v-model="communityJoined" />
            </div>

            <div class="grid-item">
                <label>Bậc thuế (*)</label>
                <input v-if="!edit" class="input-disabled" type="text" v-model="user.tax" :disabled="true" />
                <input v-else id="tax" type="text" v-model="user.tax" />
                <div class="validation-error-container">
                    <span class="validation-error" v-if="!validation.requiredString(user.tax)">
                        {{ validationMessages['required'] }}
                    </span>
                    <span class="validation-error" v-if="!validation.number(user.tax)">
                        {{ validationMessages['number'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { NcMultiselect, NcDatetimePicker, NcListItemIcon, NcButton } from "@nextcloud/vue";
import { generateUrl } from '@nextcloud/router'
import axios from "@nextcloud/axios";
import Multiselect from "../Multiselect.vue";
import { showError, showSuccess } from '@nextcloud/dialogs'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Close from 'vue-material-design-icons/Close.vue'
import Check from 'vue-material-design-icons/Check.vue'
import validation from '../../validate.js';


export default {
    name: 'Details',
    components: {
        NcDatetimePicker,
        NcMultiselect,
        Multiselect,
        NcListItemIcon,
        NcButton,
        Check,
        Pencil,
        Close
    },
    props: {
        userId: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            user: null,
            initialUser: null,
            edit: false,
            units: [],
            positions: [],
            nc_users: [],
            genders: [
                { text: 'Nam', value: 0 },
                { text: 'Nữ', value: 1 }
            ],
            validationMessages: {
                'required': 'Không được để trống',
                'email': 'Phải là email',
                'phone': 'Gồm 10 số',
                'id_number': 'Gồm 9 hoặc 12 số',
                'full_name': 'Không chứa số hay ký tự đặc biệt',
                'number': 'Phải là số tự nhiên',
                'decimal': 'Phải là số'
            },
            selectedUser: null,
            dateOfBirth: null,
            communityJoined: null
        };
    },

    computed: {
        formatUnits() {
            return this.units.map(unit => ({
                text: `${unit.unit_id} - ${unit.unit_name}`,
                value: unit.unit_id,
            }));
        },

        formatPositions() {
            return this.positions.map(position => ({
                text: `${position.position_id} - ${position.position_name}`,
                value: position.position_id,
            }));
        },

        formatUsers() {
            const usersArray = Object.values(this.nc_users);
            return usersArray.map(user => {
                return {
                    userId: user.user_id,
                    subtitle: user.display_name,
                    icon: 'icon-user'
                };
            });
        },

        formatNcPickerDate() {
            return new Date(this.user.date_of_birth + '')
        },

        getDateOfBirth() {
            return this.formatDateToDDMMYYYY(this.user.date_of_birth)
        },

        getCommunity() {
            return this.formatDateToDDMMYYYY(this.user.communist_party_joined)
        },

        getUnitName() {
            const matchingUnit = this.units.find(unit => unit.unit_id === this.user.unit_id);
            return matchingUnit ? matchingUnit.unit_name : '';
        },

        getPositionName() {
            const matchingPosition = this.positions.find(position => position.position_id === this.user.position_id);
            return matchingPosition ? matchingPosition.position_name : '';
        },

        getGender() {
            return this.user.gender ? 'Nữ' : 'Nam';
        },

        validation() {
            return validation;
        },
    },

    methods: {
        startEditting() {
            this.edit = true;
        },
        cancelEditting() {
            this.edit = false;
            this.user = JSON.parse(JSON.stringify(this.initialUser));
            console.log(this.user)
        },
        submitEditting() {
            this.edit = true;
            this.updateUser()
        },

        formatDateToDDMMYYYY(inputDate) {
            if (!inputDate) return 'Không';
            const parts = inputDate.split('-');
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        },

        mysqlDateFormatter(date) {
            if (!date) return '';
            if (typeof date === 'string') {
                date = new Date(date);
            }
            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        },
        async getUnits() {
            try {
                const response = await axios.get(generateUrl('apps/qlcb/units'))
                this.units = response.data.units
            } catch (e) {
                console.error(e)
            }
        },

        async getPositions() {
            try {
                const response = await axios.get(generateUrl('apps/qlcb/positions'))
                this.positions = response.data.positions
            } catch (e) {
                console.error(e)
            }
        },

        async getNCUsers() {
            try {
                const response = await axios.get(generateUrl('apps/qlcb/nc_users'))
                this.nc_users = response.data.nc_users
            } catch (e) {
                console.error(e)
            }
        },

        async updateUser() {
            try {
                console.log(this.user)
                const response = await axios.put('/apps/qlcb/update_user', {
                    qlcb_uid: this.user.qlcb_uid,
                    full_name: this.user.full_name,
                    date_of_birth: this.mysqlDateFormatter(this.dateOfBirth),
                    gender: this.user.gender.value,
                    phone: this.user.phone,
                    address: this.user.address,
                    id_number: this.user.id_number,
                    email: this.user.email,
                    position_id: this.user.position_id,
                    coefficients_salary: this.user.coefficients_salary,
                    tax: this.user.tax,
                    communist_party_joined: this.communityJoined ? this.mysqlDateFormatter(this.communityJoined) : null,
                    unit_id: this.user.unit_id,
                    day_joined: null
                });
                this.edit = false
                console.log(this.user)
                await this.getUser();
                showSuccess(t('qlcb', 'Cập nhật thành công'));
            } catch (e) {
                console.error(e)
            }
        },

        async getUser() {
            try {
                const response = await axios.get(generateUrl(`/apps/qlcb/user/${this.userId}`));
                this.user = response.data.user
                this.user.tax = this.user.tax +''
                this.initialUser = JSON.parse(JSON.stringify(this.user));
                this.selectedUser =
                {
                    userId: this.user.qlcb_uid,
                    subtitle: 'display_name',
                    icon: 'icon-user'
                }
                console.log(this.user)
                this.dateOfBirth = new Date(this.user.date_of_birth + '')
                this.communityJoined = this.user.communist_party_joined ? new Date(this.user.communist_party_joined + '') : null
            } catch (e) {
                console.error(e)
            }
        },
    },

    mounted() {
        this.getUnits()
        this.getPositions()
        this.getNCUsers()
        this.getUser()
    },
}
</script>

<style scoped>
.content {
    display: flex;
    align-items: center;
    flex-direction: column;
    justify-content: center;
    height: 100%;
}

.grid-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-column-gap: 100px;
    grid-row-gap: 25px;
    padding: 20px;
    width: 80%;
    margin: auto;
}

.grid-item {
    display: flex;
    flex-direction: column;
}

.grid-item label {
    margin-bottom: 4px;
}

.grid-item input,
.grid-item .nc-datetime-picker,
.grid-item .nc-multiselect {
    width: 100%;
}

.validation-error {
    color: red;
    font-size: 0.8em;
}

input {
    height: 44px !important
}

::v-deep .nc-picker {
    width: 100% !important;
}

::v-deep .multiselect__tags {
    border: 2px solid #949494 !important;
}

::v-deep .multiselect__tags:hover {
    border-color: #3287b5 !important;
}

::v-deep .mx-input {
    height: 44px !important;
}

.edit-button,
.combo-buttons {
    margin-left: auto;
}

.combo-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.validation-error-container {
    height: 10px;
}

.input-disabled {
    border-color: gray;
    color: gray;
    cursor: not-allowed
}
</style>