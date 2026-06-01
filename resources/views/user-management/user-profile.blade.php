<!-- page title -->
@php $pageName = "user"; $subpageName = "list_user"; @endphp

@extends('layouts.backendapp')
<style>
    .img-uploader {
  --iu-bg: #fff;
  --iu-surface: #fff;
  --iu-border: #2a2a30;
  --iu-accent: #c8f04a;
  --iu-accent-dim: rgba(200, 240, 74, 0.12);
  --iu-text: #f0efe8;
  --iu-muted: #6b6b72;
  --iu-radius: 16px;
}

.img-uploader .card {
  background: var(--iu-surface);
  border: 1px solid var(--iu-border);
  border-radius: var(--iu-radius);
  padding: 2.5rem;
  width: 100%;
  max-width: 550px;
  box-shadow: 0 32px 80px rgba(0,0,0,0.5);
}

.img-uploader .card-header { margin-bottom: 2rem; }

.img-uploader .card-header h1 {
  font-size: 1.6rem;
  font-weight: 300;
  letter-spacing: -0.02em;
  color: var(--iu-text);
}

.img-uploader .card-header p {
  font-size: 0.72rem;
  color: var(--iu-muted);
  margin-top: 0.4rem;
  letter-spacing: 0.04em;
}

.img-uploader .drop-zone {
  border: 1.5px dashed var(--iu-border);
  border-radius: 12px;
  padding: 1.5rem 1rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.25s ease;
  position: relative;
  background: transparent;
}

.img-uploader .drop-zone:hover,
.img-uploader .drop-zone.drag-over {
  border-color: var(--iu-accent);
  background: var(--iu-accent-dim);
}

.img-uploader .drop-zone input[type="file"] {
  position: absolute;
  inset: 0;
  opacity: 0;
  cursor: pointer;
  width: 100%;
  height: 100%;
}

.img-uploader .drop-icon {
  width: 48px;
  height: 48px;
  margin: 0 auto 1rem;
  background: var(--iu-accent-dim);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.2s ease;
}

.img-uploader .drop-zone:hover .drop-icon { transform: scale(1.1); }

.img-uploader .drop-icon svg {
  width: 22px;
  height: 22px;
  stroke: var(--iu-accent);
}

.img-uploader .drop-label {
  font-size: 0.95rem;
  font-weight: 300;
  color: var(--iu-text);
  margin-bottom: 0.3rem;
}

.img-uploader .drop-label span { color: var(--iu-accent); font-weight: 400; }

.img-uploader .drop-hint {
  font-size: 0.68rem;
  color: var(--iu-muted);
  letter-spacing: 0.03em;
}

.img-uploader .preview-wrapper {
  display: none;
  margin-top: 1.5rem;
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid var(--iu-border);
  animation: iu-fadeIn 0.3s ease;
}

@keyframes iu-fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}

.img-uploader .preview-wrapper.show { display: block; }

.img-uploader .preview-img {
  width: 100%;
  max-height: 280px;
  object-fit: cover;
  display: block;
}

.img-uploader .preview-bar {
  background: rgba(14,14,15,0.9);
  backdrop-filter: blur(8px);
  padding: 0.75rem 1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-top: 1px solid var(--iu-border);
}

.img-uploader .preview-info {
  font-size: 0.7rem;
  color: var(--iu-muted);
}

.img-uploader .preview-info strong {
  display: block;
  color: var(--iu-text);
  font-size: 0.75rem;
  margin-bottom: 2px;
  max-width: 260px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.img-uploader .btn-remove {
  background: none;
  border: 1px solid var(--iu-border);
  border-radius: 8px;
  color: var(--iu-muted);
  font-size: 0.68rem;
  padding: 0.35rem 0.75rem;
  cursor: pointer;
  transition: all 0.2s ease;
  letter-spacing: 0.03em;
}

.img-uploader .btn-remove:hover { border-color: #ff5c5c; color: #ff5c5c; }

.img-uploader .actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 1.5rem;
}

.img-uploader .btn {
  flex: 1;
  padding: 0.85rem 1.25rem;
  border-radius: 10px;
  font-size: 0.78rem;
  font-weight: 500;
  letter-spacing: 0.05em;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.img-uploader .btn-secondary {
  background: transparent;
  border: 1px solid var(--iu-border);
  color: var(--iu-muted);
}

.img-uploader .btn-secondary:hover { border-color: var(--iu-text); color: var(--iu-text); }

.img-uploader .btn-primary {
  background: var(--iu-accent);
  color: #0e0e0f;
  font-weight: 600;
}

.img-uploader .btn-primary:hover {
  background: #d9ff55;
  transform: translateY(-1px);
  box-shadow: 0 8px 24px rgba(200,240,74,0.25);
}

.img-uploader .btn-primary:disabled {
  background: var(--iu-border);
  color: var(--iu-muted);
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.img-uploader .toast {
  display: none;
  margin-top: 1rem;
  background: var(--iu-accent-dim);
  border: 1px solid rgba(200,240,74,0.3);
  border-radius: 10px;
  padding: 0.75rem 1rem;
  font-size: 0.72rem;
  color: var(--iu-accent);
  letter-spacing: 0.03em;
  animation: iu-fadeIn 0.3s ease;
}

.img-uploader .toast.show { display: block; }


</style>

@section('content')
                  
<div class="container-fluid mt-3">
    <div class="bg-theme-1-subtle rounded px-3 py-3">
        <div class="row gx-3 align-items-center">
            <div class="col col-sm mb-2 mb-sm-0">
                <p class="h5">User Management</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item bi"><a href="#">User Management</a></li>
                        <li class="breadcrumb-item bi"><a href="#">User Profile</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Update Profile</a></li>
                         
                    </ol>
                </nav>
            </div>
            <div class="col-auto ">
            </div>
        </div>
    </div>
     
        
</div>
 <div class="container mt-3" id="main-content">

    <!-- welcome bar -->
    <div class="row gx-3 align-self-center ">
        <!-- welcome message -->
        <div class="col-12 col-sm mb-3 mb-lg-4">
            <p class="h2 fw-normal mb-0">Welcome to your account,</p>
            <h1 class="display-3 fw-medium"><span class="text-gradient">{{auth()->user()->name}}</span></h1>
        </div>

        
    </div>

    <div class="row gx-3 gx-lg-4">
        <div class="col-12 col-lg-5 col-xl-4">

            <!-- profile details -->
            <div class="card adminuiux-card shadow-sm mb-3 mb-lg-4 overflow-hidden">
                <figure class="height-150 w-100 coverimg z-index-0 blur-overlay blur-theme">
                    <img src="{{asset('backend/assets/img/modern-ai-image/user-1.jpg')}}" alt="" />
                </figure>

                <div class="card-body pt-0">
                    <div class="mt--100 text-center position-relative z-index-0 mb-3 mb-lg-4">
                        <figure class="avatar avatar-180 coverimg rounded-circle mx-auto z-index-1">
                            <img src="{{$user->picture}}" alt="" />
                        </figure>
                    </div>
                    <p class="text-secondary mb-2"><i class="bi bi-people me-2 align-middle text-theme-1"></i> <span class="align-middle"> {{auth()->user()->getUserCategory()}}</span></p>
                    <p class="text-secondary mb-2"><i class="bi bi-question-circle me-2 align-middle text-theme-1"></i> <span class="align-middle">{{ $user->personal_email }}</span></p>
                    <p class="text-secondary mb-2"><i class="bi bi-telephone me-2 align-middle text-theme-1"></i> <span class="align-middle">{{ $user->contact_num }} </span></p>
                    <p class="text-secondary mb-2"><i class="bi bi-cake me-2 align-middle text-theme-1"></i> <span class="align-middle">{{ $user->position }}</span></p>
                  
                    <p class="text-secondary d-flex"><i class="bi bi-geo-alt me-2 align-middle text-theme-1"></i> <span class="align-middle">{{ $user->personal_address }}.</span></p>
                </div>
                {{-- <div class="card-footer justify-content-center text-center">
                    <a href="adminux-account-setting.html" class="btn btn-link"><i class="bi bi-gear"></i> Update Profile</a>
                </div> --}}
            </div>

           
            
        </div>
        <div class="col-12 col-lg-7 col-xl-8">

            <!-- alert -->
            <div class="alert alert-info alert-dismissible mb-3 mb-lg-4" role="alert">
                {{-- <button type="button" class="btn btn-square btn-sm btn-link position-absolute top-0 end-0 m-2" data-bs-dismiss="alert"><i class="bi bi-x-lg"></i></button> --}}
                <div class="row gx-3 align-items-center mb-3">
                    <div class="col-auto">
                        <i class="bi bi-gear h4 me-1 avatar avatar-40 rounded"></i>
                    </div>
                    <div class="col">
                        <h6>Notification</h6>
                    </div>
                </div>
                <div class="row gx-3 align-items-center">
                    <div class="col-12 col-sm">
                        <p>Ensure you never lose access to your account change your password anytime you think it's been compromised to safe you.</p>
                    </div>
                     
                </div>
            </div>

            
            <!-- background -->
            <div class="card adminuiux-card shadow-sm mb-3 mb-lg-4">
                <div class="card-header">
                    <div class="row gx-3 align-items-center">
                        <div class="col-auto">
                            <i class="bi bi-camera h5 me-1 avatar avatar-40 bg-theme-1-subtle text-theme-1 rounded "></i>
                        </div>
                        <div class="col">
                            <h6 class="mb-0">Change Photo</h6>
                            
                        </div>
                        
                    </div>
                </div>
                <div class="card-body pb-0">
                    <div class="row ">
                        <form enctype="multipart/form-data" method="POST" action="{{ route('update-user-photo-process') }}">
                            @csrf
                            <div class="img-uploader">

                                <div class="card">
                                    <div class="card-header">
                                    <h1 style="color:#0e0e0f">Upload Image</h1>
                                    <p style="color:#0e0e0f">SELECT A FILE — PREVIEW BEFORE SAVING</p>
                                    </div>

                                    <div class="drop-zone" id="dropZone">
                                    <input type="file" id="fileInput" name="image" accept="image/*" required>
                                    <div class="drop-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="17 8 12 3 7 8"/>
                                        <line x1="12" y1="3" x2="12" y2="15"/>
                                        </svg>
                                    </div>
                                    <p class="drop-label"><span>Click to upload</span> or drag & drop</p>
                                    <p class="drop-hint">PNG, JPG, WEBP, GIF — MAX 10MB</p>
                                    </div>

                                    <div class="preview-wrapper" id="previewWrapper">
                                    <img class="preview-img" id="previewImg" src="" alt="Preview">
                                    <div class="preview-bar">
                                        <div class="preview-info">
                                        <strong id="fileName">—</strong>
                                        <span id="fileSize">—</span>
                                        </div>
                                        <button class="btn-remove" id="removeBtn">✕ Remove</button>
                                    </div>
                                    </div>

                                    
                                </div>

                            </div><br>
                            
                                <div class="actions" style="margin-top: 30px;margin-bottom:30px">
                                        <button class="btn btn-danger" id="cancelBtn">Cancel</button>
                                        <button type="submit" class="btn btn-success"   >Save Image</button>
                                </div>
                                <input type="hidden" name="staff_id" value="{{ $user->staff_id }}"/>
                        </form>
                            
                    </div>

                 
                </div>
            </div>

            <!-- background -->
            <div class="card adminuiux-card shadow-sm mb-3 mb-lg-4">
                <div class="card-header">
                    <div class="row gx-3 align-items-center">
                        <div class="col-auto">
                            <i class="bi bi-lock h5 me-1 avatar avatar-40 bg-theme-1-subtle text-theme-1 rounded "></i>
                        </div>
                        <div class="col">
                            <h6 class="mb-0">Change Password</h6>
                            
                        </div>
                        
                    </div>
                </div>
                <div class="card-body pb-0">
                    <div class="row ">
                        <form enctype="multipart/form-data" method="POST" action="{{ route('update-user-password-process') }}">
                            @csrf
                            
                            <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="password" name="current_password" class="form-control"   placeholder="Current Password">
                                        <label>Current Password</label>
                                        @error('current_password') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                            </div>
                            <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="password" name="new_password" class="form-control"   placeholder="New Password">
                                        <label>New Password</label>
                                        @error('new_password') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                            </div>
                            <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="password" name="confirm_password" class="form-control"   placeholder="Confirm Password">
                                        <label>Confirm Password</label>
                                        @error('confirm_password') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                            </div>
                               
                         <input type="hidden" name="staff_id" value="{{ $user->staff_id }}"/>

                         <div class="mb-3">
                                    <button type="submit" class="btn btn-success">Change Password </button>
                                </div>
                        </form>
                            
                    </div>

                 
                </div>
            </div>


           

        </div>
    </div>
</div>


 
 @endsection

@section('scripts')
 <script>
  

    
@if(session('message_success'))
Swal.fire({
    icon: 'success',
    title: 'Success',
    text: "{{ session('message_success') }}",
    showConfirmButton: true,
    timer: 2000
});
@endif
@if(session('error_message'))
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: "{{ session('error_message') }}",
     showConfirmButton: true,
    timer: 2000
});
@endif
</script>

 <script>
  const dropZone       = document.getElementById('dropZone');
  const fileInput      = document.getElementById('fileInput');
  const previewWrapper = document.getElementById('previewWrapper');
  const previewImg     = document.getElementById('previewImg');
  const fileName       = document.getElementById('fileName');
  const fileSize       = document.getElementById('fileSize');
  const removeBtn      = document.getElementById('removeBtn');
  const saveBtn        = document.getElementById('saveBtn');
  const cancelBtn      = document.getElementById('cancelBtn');
  const toast          = document.getElementById('toast');

  function formatSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
  }

  function loadFile(file) {
    if (!file || !file.type.startsWith('image/')) return;
    const reader = new FileReader();
    reader.onload = e => {
      previewImg.src = e.target.result;
      fileName.textContent = file.name;
      fileSize.textContent = formatSize(file.size);
      previewWrapper.classList.add('show');
      saveBtn.disabled = false;
      toast.classList.remove('show');
    };
    reader.readAsDataURL(file);
  }

  fileInput.addEventListener('change', e => loadFile(e.target.files[0]));

  // Drag & drop
  dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
  dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
  dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    loadFile(e.dataTransfer.files[0]);
  });

  function reset() {
    previewWrapper.classList.remove('show');
    previewImg.src = '';
    fileInput.value = '';
    saveBtn.disabled = true;
    toast.classList.remove('show');
  }

  removeBtn.addEventListener('click', reset);
  cancelBtn.addEventListener('click', reset);

  // Save - AJAX to Laravel
  saveBtn.addEventListener('click', () => {
    const formData = new FormData();
    formData.append('image', fileInput.files[0]);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    fetch('/upload-image', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      toast.classList.add('show');
      saveBtn.disabled = true;
    })
    .catch(err => console.error('Upload failed:', err));
  });
</script>
 
@endsection