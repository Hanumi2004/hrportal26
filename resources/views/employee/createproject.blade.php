@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold">Create New Project</h2>
                <p class="text-muted">Define a new project and track its progress.</p>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($duplicateProject = session('duplicate_project'))
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4">
            <strong>Similar project name detected!</strong>
            <p class="mb-2 mt-1">
                "{{ old('project_name') }}" is similar to existing project
                <strong>"{{ $duplicateProject['name'] }}"</strong>.
                Is this the same project?
            </p>
            <div class="d-flex gap-2">
                <form action="{{ route('project.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="project_name" value="{{ old('project_name') }}">
                    <input type="hidden" name="project_desc" value="{{ old('project_desc') }}">
                    <input type="hidden" name="start_date" value="{{ old('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ old('end_date') }}">
                    <input type="hidden" name="project_status" value="{{ old('project_status') }}">
                    <input type="hidden" name="confirm_update" value="1">
                    <input type="hidden" name="duplicate_id" value="{{ $duplicateProject['id'] }}">
                    <button type="submit" class="btn btn-warning">
                        Yes, Update Existing
                    </button>
                </form>
                <form action="{{ route('project.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="project_name" value="{{ old('project_name') }}">
                    <input type="hidden" name="project_desc" value="{{ old('project_desc') }}">
                    <input type="hidden" name="start_date" value="{{ old('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ old('end_date') }}">
                    <input type="hidden" name="project_status" value="{{ old('project_status') }}">
                    <input type="hidden" name="force_create" value="1">
                    <button type="submit" class="btn btn-primary">
                        No, Create New
                    </button>
                </form>
            </div>
        </div>
    @endif

    <form action="{{ route('project.store') }}" method="POST">
        @csrf
        <div class="card shadow-sm border-0 rounded-4 p-4">
            <div class="row g-4">
                <div class="col-lg-8">
                    <label class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
                    <input type="text" name="project_name" value="{{ old('project_name') }}"
                           class="form-control @error('project_name') is-invalid @enderror"
                           placeholder="Enter project name">
                    @error('project_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="project_desc" rows="4"
                              class="form-control @error('project_desc') is-invalid @enderror"
                              placeholder="Describe the project scope and objectives">{{ old('project_desc') }}</textarea>
                    @error('project_desc')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-4">
                    <label class="form-label fw-semibold">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                           class="form-control @error('start_date') is-invalid @enderror">
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-4">
                    <label class="form-label fw-semibold">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                           class="form-control @error('end_date') is-invalid @enderror">
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-4">
                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="project_status" class="form-select @error('project_status') is-invalid @enderror" required>
                        <option value="">-- Select Status --</option>
                        <option value="not-started" {{ old('project_status') == 'not-started' ? 'selected' : '' }}>Not Started</option>
                        <option value="in-progress" {{ old('project_status') == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="on-hold" {{ old('project_status') == 'on-hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ old('project_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('project_status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('project.index.employee') }}" class="btn btn-light">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">Create Project</button>
            </div>
        </div>
    </form>
</div>
@endsection
