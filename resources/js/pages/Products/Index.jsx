import ResourceIndex from '../../components/admin/ResourceIndex';

export default function ProductsIndex(props) {
    return <ResourceIndex resource="products" records={props.products} {...props} />;
}
