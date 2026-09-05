@foreach ($users as $user)
    <option value="{{ $user->id }}" {{ Auth::user()->name == $user->name ? 'selected' : '' }}>{{ $user->name }}
    </option>
@endforeach