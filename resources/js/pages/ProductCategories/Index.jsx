import ResourceIndex from '../../components/admin/ResourceIndex';

export default function ProductCategoriesIndex(props) {
    return <ResourceIndex resource="categories" records={props.categories} {...props} />;
}
