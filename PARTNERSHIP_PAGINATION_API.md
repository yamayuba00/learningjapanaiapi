# Partnership API with Pagination

API Partnership dengan pagination untuk JLPT Classes dan Internships.

## Get JLPT Classes (with Pagination)

### Endpoint
**GET** `/api/mobile/partnership/jlpt-classes`

### Headers
```
Authorization: Bearer {token}
```

### Query Parameters
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `per_page` | integer | 10 | Number of items per page (max: 50) |
| `page` | integer | 1 | Page number |

### Request Example
```
GET /api/mobile/partnership/jlpt-classes?per_page=10&page=1
```

### Response Success
```json
{
  "success": true,
  "message": "JLPT classes retrieved successfully",
  "data": {
    "items": [
      {
        "uid": "uuid-1",
        "name": "JLPT N5 Intensive Course",
        "description": "Comprehensive N5 preparation course",
        "level": "N5",
        "duration": "3 months",
        "price": "Rp 2,500,000",
        "location": "Jakarta",
        "contact_email": "info@jlptclass.com",
        "contact_phone": "+6281234567890",
        "website_url": "https://jlptclass.com",
        "logo_url": "https://example.com/logo.jpg",
        "display_order": 1,
        "is_active": true,
        "is_verified": true
      },
      {
        "uid": "uuid-2",
        "name": "JLPT N4 Weekend Class",
        "description": "Weekend intensive N4 course",
        "level": "N4",
        "duration": "4 months",
        "price": "Rp 3,000,000",
        "location": "Bandung",
        "contact_email": "info@jlptn4.com",
        "contact_phone": "+6281234567891",
        "website_url": "https://jlptn4.com",
        "logo_url": "https://example.com/logo2.jpg",
        "display_order": 2,
        "is_active": true,
        "is_verified": true
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total": 25,
      "total_pages": 3,
      "has_more": true
    }
  }
}
```

---

## Get Internships (with Pagination)

### Endpoint
**GET** `/api/mobile/partnership/internships`

### Headers
```
Authorization: Bearer {token}
```

### Query Parameters
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `per_page` | integer | 10 | Number of items per page (max: 50) |
| `page` | integer | 1 | Page number |

### Request Example
```
GET /api/mobile/partnership/internships?per_page=10&page=1
```

### Response Success
```json
{
  "success": true,
  "message": "Internships retrieved successfully",
  "data": {
    "items": [
      {
        "uid": "uuid-1",
        "name": "Software Engineer Internship",
        "company_name": "Tech Company Japan",
        "description": "6-month internship program",
        "location": "Tokyo, Japan",
        "duration": "6 months",
        "requirements": "Japanese N3 level, Programming skills",
        "benefits": "Monthly allowance, Accommodation",
        "application_deadline": "2026-06-30",
        "contact_email": "hr@techcompany.jp",
        "contact_phone": "+81-3-1234-5678",
        "website_url": "https://techcompany.jp/internship",
        "logo_url": "https://example.com/company-logo.jpg",
        "success_rate": 85,
        "display_order": 1,
        "is_active": true,
        "is_verified": true
      },
      {
        "uid": "uuid-2",
        "name": "Hotel Management Internship",
        "company_name": "Luxury Hotel Group",
        "description": "Hospitality internship in Osaka",
        "location": "Osaka, Japan",
        "duration": "12 months",
        "requirements": "Japanese N2 level, Hospitality experience",
        "benefits": "Salary, Housing, Meals",
        "application_deadline": "2026-07-15",
        "contact_email": "careers@luxuryhotel.jp",
        "contact_phone": "+81-6-1234-5678",
        "website_url": "https://luxuryhotel.jp/careers",
        "logo_url": "https://example.com/hotel-logo.jpg",
        "success_rate": 92,
        "display_order": 2,
        "is_active": true,
        "is_verified": true
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total": 18,
      "total_pages": 2,
      "has_more": true
    }
  }
}
```

---

## Pagination Object

### Fields
| Field | Type | Description |
|-------|------|-------------|
| `current_page` | integer | Current page number |
| `per_page` | integer | Items per page |
| `total` | integer | Total number of items |
| `total_pages` | integer | Total number of pages |
| `has_more` | boolean | Whether there are more pages |

---

## Mobile App Implementation

### React Native Example
```javascript
import { useState, useEffect } from 'react';

const JlptClassesScreen = () => {
  const [classes, setClasses] = useState([]);
  const [pagination, setPagination] = useState(null);
  const [loading, setLoading] = useState(false);
  const [page, setPage] = useState(1);
  
  const fetchClasses = async (pageNum = 1) => {
    setLoading(true);
    try {
      const response = await api.get('/partnership/jlpt-classes', {
        params: {
          per_page: 10,
          page: pageNum
        }
      });
      
      const { items, pagination } = response.data;
      
      if (pageNum === 1) {
        setClasses(items);
      } else {
        // Append for infinite scroll
        setClasses(prev => [...prev, ...items]);
      }
      
      setPagination(pagination);
    } catch (error) {
      console.error('Failed to fetch classes:', error);
    } finally {
      setLoading(false);
    }
  };
  
  useEffect(() => {
    fetchClasses(1);
  }, []);
  
  const loadMore = () => {
    if (pagination?.has_more && !loading) {
      fetchClasses(page + 1);
      setPage(page + 1);
    }
  };
  
  return (
    <FlatList
      data={classes}
      renderItem={({ item }) => <ClassCard class={item} />}
      keyExtractor={item => item.uid}
      onEndReached={loadMore}
      onEndReachedThreshold={0.5}
      ListFooterComponent={
        loading && <ActivityIndicator />
      }
    />
  );
};
```

### Flutter Example
```dart
class JlptClassesScreen extends StatefulWidget {
  @override
  _JlptClassesScreenState createState() => _JlptClassesScreenState();
}

class _JlptClassesScreenState extends State<JlptClassesScreen> {
  List<JlptClass> classes = [];
  Pagination? pagination;
  bool isLoading = false;
  int currentPage = 1;
  
  ScrollController _scrollController = ScrollController();
  
  @override
  void initState() {
    super.initState();
    fetchClasses(1);
    
    _scrollController.addListener(() {
      if (_scrollController.position.pixels ==
          _scrollController.position.maxScrollExtent) {
        loadMore();
      }
    });
  }
  
  Future<void> fetchClasses(int page) async {
    if (isLoading) return;
    
    setState(() => isLoading = true);
    
    try {
      final response = await api.get(
        '/partnership/jlpt-classes',
        queryParameters: {
          'per_page': 10,
          'page': page,
        },
      );
      
      final data = response.data['data'];
      final items = (data['items'] as List)
          .map((json) => JlptClass.fromJson(json))
          .toList();
      
      setState(() {
        if (page == 1) {
          classes = items;
        } else {
          classes.addAll(items);
        }
        pagination = Pagination.fromJson(data['pagination']);
        currentPage = page;
      });
    } catch (e) {
      print('Failed to fetch classes: $e');
    } finally {
      setState(() => isLoading = false);
    }
  }
  
  void loadMore() {
    if (pagination?.hasMore == true && !isLoading) {
      fetchClasses(currentPage + 1);
    }
  }
  
  @override
  Widget build(BuildContext context) {
    return ListView.builder(
      controller: _scrollController,
      itemCount: classes.length + (isLoading ? 1 : 0),
      itemBuilder: (context, index) {
        if (index == classes.length) {
          return Center(child: CircularProgressIndicator());
        }
        return ClassCard(class: classes[index]);
      },
    );
  }
}
```

---

## Benefits of Pagination

### 🚀 **Performance**
- Faster initial load time
- Reduced memory usage
- Better app responsiveness

### 📱 **User Experience**
- Smooth scrolling
- Infinite scroll support
- Progressive loading

### 🔧 **Backend Efficiency**
- Reduced database load
- Lower bandwidth usage
- Scalable for large datasets

---

## Query Parameter Validation

### Default Values
- `per_page`: 10 (if not provided)
- `page`: 1 (if not provided)

### Limits
- `per_page`: Maximum 50 items
- `page`: Minimum 1

### Invalid Parameters
If invalid parameters are provided, defaults will be used:
```
GET /partnership/jlpt-classes?per_page=0&page=-1
// Will use: per_page=10, page=1
```

---

## Error Handling

### No Data Available
```json
{
  "success": true,
  "message": "JLPT classes retrieved successfully",
  "data": {
    "items": [],
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total": 0,
      "total_pages": 0,
      "has_more": false
    }
  }
}
```

### Server Error
```json
{
  "success": false,
  "message": "Failed to get JLPT classes: Database connection error"
}
```

---

## Testing

### Test Page 1
```bash
curl -X GET "/api/mobile/partnership/jlpt-classes?per_page=10&page=1" \
  -H "Authorization: Bearer {token}"
```

### Test Page 2
```bash
curl -X GET "/api/mobile/partnership/jlpt-classes?per_page=10&page=2" \
  -H "Authorization: Bearer {token}"
```

### Test Different Page Size
```bash
curl -X GET "/api/mobile/partnership/jlpt-classes?per_page=5&page=1" \
  -H "Authorization: Bearer {token}"
```

---

## Status Codes

- `200 OK`: Data retrieved successfully
- `401 Unauthorized`: Invalid or missing token
- `500 Internal Server Error`: Server error