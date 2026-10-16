import ResourceIndex from '../../components/admin/ResourceIndex';

export default function SuppliersIndex(props) {
    return <ResourceIndex resource="suppliers" records={props.suppliers} {...props} />;
}
