@extends('admin.index')

@section('admin')
@include('admin.components.alert')
<div class="container p-2">
	<form id="formContainerAdmin" method="POST">
	<div class="btn-group pt-5 pb-3"></div>
	</form>
</div>

@endsection


@section('css_styles')
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/style.form.css')}}">
@endsection


@section('js_script')
@endsection

@section('js_form')

<script type="module">
	import { formDataTable } from "{{asset('admin/js/formDataTable.js')}}";
	const DataConfigForm = async () => {
		const data = new formDataTable();
		await data.load();
		return data;
	}
	DataConfigForm();
</script>

@endsection