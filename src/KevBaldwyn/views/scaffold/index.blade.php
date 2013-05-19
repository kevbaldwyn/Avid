<ul>
@foreach ($list as $item)
    <li><a href="{{ URL::route('admin.' . $item->getTable() . '.edit', array($item->id)) }}">{{ $item->name }}</a></li>
@endforeach
</ul>