/* Student form validation and modal behaviour. */
document.addEventListener("DOMContentLoaded", function () {
    "use strict";
    const form = document.getElementById("studentForm");
    if (!form) return;
    form.noValidate = true;
    const photoContainer = document.getElementById("photoContainer"), photo = document.getElementById("studentPhoto"),
        preview = document.getElementById("photoPreview"), photoIcon = document.getElementById("photoIcon"),
        modal = document.getElementById("addStdModal"), addButton = document.getElementById("addStdBtn"),
        saveButton = document.getElementById("saveStdBtn"), title = document.getElementById("modalTitle"),
        studentId = document.getElementById("studentId");
    const fields = {};
    ["fullName", "email", "phone", "dob", "program", "semester", "address", "password", "confirmPassword"].forEach(id => fields[id] = document.getElementById(id));
    let allowSubmission = false, hasSubmitted = false, verifiedPhotoFile = null, verifiedPhotoIsValid = false;
    const isEdit = () => studentId.value !== "" || /update_std\.php(?:$|[?#])/i.test(form.action);
    const container = input => input.closest(".mb-3, .mb-0") || input.parentElement;
    const feedback = input => container(input).querySelector('.validation-error[data-for="' + input.id + '"]');
    const empty = input => input === fields.phone ? input.value === "+977" : !input.value.trim();
    const clear = input => { input.classList.remove("is-invalid", "is-valid"); if (input === photo) photoContainer.classList.remove("border-danger", "border-success"); const box = feedback(input); if (box) box.remove(); };
    const error = (input, text) => { input.classList.remove("is-valid"); input.classList.add("is-invalid"); if (input === photo) photoContainer.classList.add("border-danger"); let box = feedback(input); if (!box) { box = document.createElement("div"); box.className = "validation-error invalid-feedback d-block"; box.dataset.for = input.id; container(input).appendChild(box); } box.textContent = text; return false; };
    const success = input => { clear(input); input.classList.add("is-valid"); if (input === photo) photoContainer.classList.add("border-success"); return true; };
    // Do not make an untouched/cleared field red before the first submit.
    const quietIfEmpty = input => { if (!hasSubmitted && empty(input)) { clear(input); return true; } return false; };
    const localDate = date => new Date(date.getTime() - date.getTimezoneOffset() * 60000).toISOString().slice(0, 10);
    const today = new Date(); today.setHours(0, 0, 0, 0);
    const earliest = new Date(today); earliest.setFullYear(earliest.getFullYear() - 60);
    const minDob = localDate(earliest), maxDob = localDate(today);
    fields.dob.min = minDob; fields.dob.max = maxDob;
    const name = () => { const v = fields.fullName.value.trim(); if (quietIfEmpty(fields.fullName)) return true; if (!v) return error(fields.fullName, "Full name is required."); if (v.length < 2 || v.length > 50) return error(fields.fullName, "Use 2 to 50 letters and spaces."); return /^[A-Za-z]+(?: [A-Za-z]+)*$/.test(v) ? success(fields.fullName) : error(fields.fullName, "Use letters and spaces only."); };
    const email = () => { const v = fields.email.value.trim(); if (quietIfEmpty(fields.email)) return true; if (!v) return error(fields.email, "Email address is required."); const pattern = /^[A-Za-z0-9]+(?:[._+-][A-Za-z0-9]+)*@(?:[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?\.)+[A-Za-z]{2,63}$/; return pattern.test(v) && !v.includes("..") ? success(fields.email) : error(fields.email, "Enter a valid organization email address."); };
    const normalizePhone = () => { let digits = fields.phone.value.replace(/\D/g, ""); if (digits.startsWith("977")) digits = digits.slice(3); fields.phone.value = "+977" + digits.slice(0, 10); };
    const phoneNumber = () => { if (quietIfEmpty(fields.phone)) return true; const v = fields.phone.value; if (v === "+977") return error(fields.phone, "Phone number is required."); return /^\+977(?:97|98)\d{8}$/.test(v) ? success(fields.phone) : error(fields.phone, "Use +977 followed by 97 or 98 and 8 digits."); };
    const validatePhoneWhileTyping = () => {
        const v = fields.phone.value;
        // Keep incomplete numbers quiet until submit; the user is still typing.
        if (v === "+977" || v.length < 14) { clear(fields.phone); return true; }
        return phoneNumber();
    };
    const dob = () => { const v = fields.dob.value; if (quietIfEmpty(fields.dob)) return true; if (!v) return error(fields.dob, "Date of birth is required."); return v >= minDob && v <= maxDob ? success(fields.dob) : error(fields.dob, "Select a date within the last 60 years."); };
    const program = () => { if (quietIfEmpty(fields.program)) return true; const allowed = Array.from(fields.program.options).filter(o => o.value).map(o => o.value); return allowed.includes(fields.program.value) ? success(fields.program) : error(fields.program, "Select a valid program."); };
    const semester = () => { if (quietIfEmpty(fields.semester)) return true; const v = Number(fields.semester.value); return Number.isInteger(v) && v >= 1 && v <= 8 ? success(fields.semester) : error(fields.semester, "Select a semester from 1 to 8."); };
    const address = () => { const v = fields.address.value.trim(); if (quietIfEmpty(fields.address)) return true; if (!v) return error(fields.address, "Address is required."); return v.length >= 3 && v.length <= 150 && /[A-Za-z0-9]/.test(v) ? success(fields.address) : error(fields.address, "Enter a valid address (3–150 characters)."); };
    const passwordHint = () => {
        // Keep exactly one short, always-visible password requirement hint.
        document.querySelectorAll(".password-requirement-hint").forEach(hint => {
            if (hint.id !== "passwordStrength") hint.remove();
        });
        let hint = document.getElementById("passwordStrength");
        if (!hint) { hint = document.createElement("div"); hint.id = "passwordStrength"; fields.password.parentElement.appendChild(hint); }
        hint.className = "password-requirement-hint small mt-1 text-muted";
        hint.textContent = "At least 8 characters, 1 uppercase letter, and 1 special character.";
    };
    const password = () => { passwordHint(); const v = fields.password.value; if (!v && isEdit()) { clear(fields.password); return true; } if (quietIfEmpty(fields.password)) return true; if (!v) return error(fields.password, "Password is required."); return v.length >= 8 && /[A-Z]/.test(v) && /[^A-Za-z0-9]/.test(v) ? success(fields.password) : error(fields.password, "Password does not meet the requirement."); };
    const confirmPassword = () => { const p = fields.password.value, v = fields.confirmPassword.value; if (isEdit() && !p && !v) { clear(fields.confirmPassword); return true; } if (quietIfEmpty(fields.confirmPassword)) return true; if (!v) return error(fields.confirmPassword, "Please confirm the password."); return v === p ? success(fields.confirmPassword) : error(fields.confirmPassword, "Passwords do not match."); };
    const validSignature = bytes => (bytes.length >= 3 && bytes[0] === 255 && bytes[1] === 216 && bytes[2] === 255) || (bytes.length >= 8 && bytes[0] === 137 && bytes[1] === 80 && bytes[2] === 78 && bytes[3] === 71 && bytes[4] === 13 && bytes[5] === 10 && bytes[6] === 26 && bytes[7] === 10);
    const decodes = file => new Promise(resolve => { const img = new Image(), url = URL.createObjectURL(file); img.onload = () => { URL.revokeObjectURL(url); resolve(true); }; img.onerror = () => { URL.revokeObjectURL(url); resolve(false); }; img.src = url; });
    const validatePhoto = async () => { const file = photo.files && photo.files[0]; if (!file) { verifiedPhotoFile = null; verifiedPhotoIsValid = false; if (isEdit()) { clear(photo); return true; } return error(photo, "A student photo is required."); } if (verifiedPhotoFile === file) return verifiedPhotoIsValid ? success(photo) : false; if (!/\.(jpe?g|png)$/i.test(file.name) || !["image/jpeg", "image/png", ""].includes(file.type)) return error(photo, "Upload a JPG, JPEG, or PNG image only."); if (file.size > 2 * 1024 * 1024) return error(photo, "Photo must be 2 MB or smaller."); let actual = false; try { actual = validSignature(new Uint8Array(await file.slice(0, 8).arrayBuffer())) && await decodes(file); } catch (_) {} verifiedPhotoFile = file; verifiedPhotoIsValid = actual; return actual ? success(photo) : error(photo, "The selected file is not a valid image."); };
    const resetPreview = () => { preview.src = ""; preview.classList.add("d-none"); photoIcon.classList.remove("d-none"); };
    const previewPhoto = file => { const reader = new FileReader(); reader.onload = e => { preview.src = e.target.result; preview.classList.remove("d-none"); photoIcon.classList.add("d-none"); }; reader.readAsDataURL(file); };
    const photoLabel = photoContainer.parentElement.querySelector("label"); if (photoLabel && !photoLabel.querySelector(".text-danger")) photoLabel.insertAdjacentHTML("beforeend", ' <span class="text-danger">*</span>');
    photoContainer.addEventListener("click", () => photo.click());
    photo.addEventListener("change", async () => { verifiedPhotoFile = null; verifiedPhotoIsValid = false; const ok = await validatePhoto(); if (ok && photo.files[0]) previewPhoto(photo.files[0]); else if (!ok) resetPreview(); });
    fields.fullName.addEventListener("input", name); fields.email.addEventListener("input", email);
    fields.phone.addEventListener("focus", normalizePhone); fields.phone.addEventListener("input", () => { normalizePhone(); validatePhoneWhileTyping(); });
    fields.dob.addEventListener("change", dob); fields.program.addEventListener("change", program); fields.semester.addEventListener("change", semester); fields.address.addEventListener("input", address);
    fields.password.addEventListener("input", () => { password(); if (fields.confirmPassword.value || hasSubmitted) confirmPassword(); }); fields.confirmPassword.addEventListener("input", confirmPassword);
    form.addEventListener("submit", async event => { if (allowSubmission) { allowSubmission = false; return; } event.preventDefault(); hasSubmitted = true; const ok = (await Promise.all([name(), email(), phoneNumber(), dob(), program(), semester(), address(), password(), confirmPassword(), validatePhoto()])).every(Boolean); if (!ok) { const first = form.querySelector(".is-invalid"); if (first) { first.focus(); first.scrollIntoView({ behavior: "smooth", block: "center" }); } return; } allowSubmission = true; form.requestSubmit(event.submitter || saveButton); });
    document.querySelectorAll(".edit-student").forEach(button => button.addEventListener("click", async function () { try { const response = await fetch("../assets/ajax/get_std.php?id=" + encodeURIComponent(this.dataset.id)); if (!response.ok) throw new Error("Unable to load student details."); const s = await response.json(); studentId.value = s.student_id || ""; fields.fullName.value = s.full_name || ""; fields.email.value = s.email || ""; fields.phone.value = s.phone || ""; normalizePhone(); fields.dob.value = s.dob || ""; fields.program.value = s.program || ""; fields.semester.value = s.semester || ""; fields.address.value = s.address || ""; fields.password.value = ""; fields.confirmPassword.value = ""; photo.value = ""; verifiedPhotoFile = null; verifiedPhotoIsValid = false; hasSubmitted = false; Object.values(fields).forEach(clear); clear(photo); passwordHint(); if (s.photo) { preview.src = "../assets/uploads/" + encodeURIComponent(s.photo); preview.classList.remove("d-none"); photoIcon.classList.add("d-none"); } else resetPreview(); form.action = "update_std.php"; title.textContent = "Edit Student"; saveButton.innerHTML = '<i class="bi bi-pencil-square"></i> Update Student'; bootstrap.Modal.getOrCreateInstance(modal).show(); } catch (err) { alert(err.message || "Unable to load student details."); } }));
    addButton.addEventListener("click", () => { form.reset(); form.action = "save_std.php"; studentId.value = ""; normalizePhone(); verifiedPhotoFile = null; verifiedPhotoIsValid = false; hasSubmitted = false; Object.values(fields).forEach(clear); clear(photo); resetPreview(); passwordHint(); title.textContent = "Add New Student"; saveButton.innerHTML = '<i class="bi bi-floppy"></i> Save Student'; });
    document.querySelectorAll(".delete-std").forEach(button => button.addEventListener("click", function () { if (confirm("Are you sure you want to delete this student?")) window.location.href = "delete_std.php?id=" + encodeURIComponent(this.dataset.id); }));
    normalizePhone(); passwordHint();
});

