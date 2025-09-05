#!/usr/bin/env python3
"""
FastAPI Laravel Proxy Endpoints Test
Testing the newly added Laravel API proxy endpoints in FastAPI backend
"""

import requests
import json
import sys
from typing import Dict, Any, List

# Get the backend URL from frontend environment
BACKEND_URL = "https://quran-connect-2.preview.emergentagent.com/api"

class FastAPILaravelProxyTester:
    def __init__(self):
        self.base_url = BACKEND_URL
        self.session = requests.Session()
        self.session.headers.update({
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        })
        self.test_results = []
        
    def log_test(self, test_name: str, success: bool, message: str, details: Dict = None):
        """Log test results"""
        result = {
            'test': test_name,
            'success': success,
            'message': message,
            'details': details or {}
        }
        self.test_results.append(result)
        status = "✅ PASS" if success else "❌ FAIL"
        print(f"{status} {test_name}: {message}")
        if details:
            print(f"   Details: {json.dumps(details, indent=2, ensure_ascii=False)}")
        print()

    def test_basic_api_connectivity(self):
        """Test basic API connectivity"""
        print("🔍 Testing Basic API Connectivity...")
        
        try:
            response = self.session.get(f"{self.base_url}/", timeout=10)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('message') == 'Hello World':
                    self.log_test(
                        "Basic API Connectivity",
                        True,
                        "FastAPI backend is responding correctly",
                        {'status_code': response.status_code, 'response': data}
                    )
                else:
                    self.log_test(
                        "Basic API Connectivity",
                        False,
                        "Unexpected response format",
                        {'response': data}
                    )
            else:
                self.log_test(
                    "Basic API Connectivity",
                    False,
                    f"API returned status {response.status_code}",
                    {'status_code': response.status_code, 'response': response.text}
                )
                
        except requests.exceptions.RequestException as e:
            self.log_test(
                "Basic API Connectivity",
                False,
                f"Connection error: {str(e)}",
                {'error': str(e)}
            )

    def test_ahmed_abdullah_login(self):
        """Test Ahmed Abdullah's login via FastAPI proxy"""
        print("👤 Testing Ahmed Abdullah Login via FastAPI Proxy...")
        
        login_data = {
            'phone': '0501234567',
            'access_code': '4567',
            'user_type': 'guardian'
        }
        
        try:
            response = self.session.post(f"{self.base_url}/mobile/auth/login", json=login_data, timeout=10)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('data'):
                    guardian_data = data['data']['guardian']
                    if (guardian_data['name'] == 'أحمد عبدالله' and 
                        guardian_data['phone'] == '0501234567' and
                        guardian_data['students_count'] == 1):
                        self.log_test(
                            "Ahmed Abdullah Login",
                            True,
                            "Login successful with correct guardian data",
                            {
                                'guardian_name': guardian_data['name'],
                                'phone': guardian_data['phone'],
                                'students_count': guardian_data['students_count'],
                                'token_received': bool(data['data'].get('token'))
                            }
                        )
                        return data['data']['token']
                    else:
                        self.log_test(
                            "Ahmed Abdullah Login",
                            False,
                            "Login successful but incorrect guardian data",
                            {'guardian_data': guardian_data}
                        )
                else:
                    self.log_test(
                        "Ahmed Abdullah Login",
                        False,
                        "Login failed - Invalid response format",
                        {'response': data}
                    )
            else:
                self.log_test(
                    "Ahmed Abdullah Login",
                    False,
                    f"Login failed with status {response.status_code}",
                    {'status_code': response.status_code, 'response': response.text}
                )
                
        except requests.exceptions.RequestException as e:
            self.log_test(
                "Ahmed Abdullah Login",
                False,
                f"Connection error: {str(e)}",
                {'error': str(e)}
            )
        
        return None

    def test_mohammed_hassan_login(self):
        """Test Mohammed Hassan's login via FastAPI proxy"""
        print("👤 Testing Mohammed Hassan Login via FastAPI Proxy...")
        
        login_data = {
            'phone': '0501234568',
            'access_code': '4568',
            'user_type': 'guardian'
        }
        
        try:
            response = self.session.post(f"{self.base_url}/mobile/auth/login", json=login_data, timeout=10)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('data'):
                    guardian_data = data['data']['guardian']
                    if (guardian_data['name'] == 'محمد حسن' and 
                        guardian_data['phone'] == '0501234568' and
                        guardian_data['students_count'] == 1):
                        self.log_test(
                            "Mohammed Hassan Login",
                            True,
                            "Login successful with correct guardian data",
                            {
                                'guardian_name': guardian_data['name'],
                                'phone': guardian_data['phone'],
                                'students_count': guardian_data['students_count'],
                                'token_received': bool(data['data'].get('token'))
                            }
                        )
                        return data['data']['token']
                    else:
                        self.log_test(
                            "Mohammed Hassan Login",
                            False,
                            "Login successful but incorrect guardian data",
                            {'guardian_data': guardian_data}
                        )
                else:
                    self.log_test(
                        "Mohammed Hassan Login",
                        False,
                        "Login failed - Invalid response format",
                        {'response': data}
                    )
            else:
                self.log_test(
                    "Mohammed Hassan Login",
                    False,
                    f"Login failed with status {response.status_code}",
                    {'status_code': response.status_code, 'response': response.text}
                )
                
        except requests.exceptions.RequestException as e:
            self.log_test(
                "Mohammed Hassan Login",
                False,
                f"Connection error: {str(e)}",
                {'error': str(e)}
            )
        
        return None

    def test_invalid_login_credentials(self):
        """Test login with invalid credentials"""
        print("🔒 Testing Invalid Login Credentials...")
        
        invalid_credentials = [
            {'phone': '0501234567', 'access_code': 'wrong', 'user_type': 'guardian'},
            {'phone': 'wrong_phone', 'access_code': '4567', 'user_type': 'guardian'},
            {'phone': '0501234567', 'user_type': 'guardian'},  # Missing access_code
        ]
        
        for i, creds in enumerate(invalid_credentials):
            try:
                response = self.session.post(f"{self.base_url}/mobile/auth/login", json=creds, timeout=10)
                
                if response.status_code == 200:
                    data = response.json()
                    if not data.get('success'):
                        self.log_test(
                            f"Invalid Credentials Test {i+1}",
                            True,
                            f"Correctly rejected invalid credentials: {data.get('message', 'No message')}",
                            {'credentials': creds, 'response': data}
                        )
                    else:
                        self.log_test(
                            f"Invalid Credentials Test {i+1}",
                            False,
                            "Invalid credentials were accepted",
                            {'credentials': creds, 'response': data}
                        )
                else:
                    self.log_test(
                        f"Invalid Credentials Test {i+1}",
                        False,
                        f"Unexpected status code {response.status_code}",
                        {'credentials': creds, 'status_code': response.status_code}
                    )
                    
            except requests.exceptions.RequestException as e:
                self.log_test(
                    f"Invalid Credentials Test {i+1}",
                    False,
                    f"Connection error: {str(e)}",
                    {'error': str(e)}
                )

    def test_parent_dashboard(self):
        """Test parent dashboard endpoint"""
        print("📊 Testing Parent Dashboard Endpoint...")
        
        try:
            response = self.session.get(f"{self.base_url}/mobile/parent/dashboard", timeout=10)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('data'):
                    dashboard_data = data['data']
                    children = dashboard_data.get('children', [])
                    stats = dashboard_data.get('stats', {})
                    
                    if (len(children) == 1 and 
                        children[0]['name'] == 'عبدالرحمن أحمد' and
                        children[0]['id'] == 1 and
                        stats.get('total_children') == 1):
                        self.log_test(
                            "Parent Dashboard",
                            True,
                            "Dashboard returns correct Ahmed Abdullah's child data",
                            {
                                'children_count': len(children),
                                'child_name': children[0]['name'],
                                'child_id': children[0]['id'],
                                'stats': stats
                            }
                        )
                    else:
                        self.log_test(
                            "Parent Dashboard",
                            False,
                            "Dashboard returns incorrect data",
                            {'dashboard_data': dashboard_data}
                        )
                else:
                    self.log_test(
                        "Parent Dashboard",
                        False,
                        "Dashboard failed - Invalid response format",
                        {'response': data}
                    )
            else:
                self.log_test(
                    "Parent Dashboard",
                    False,
                    f"Dashboard failed with status {response.status_code}",
                    {'status_code': response.status_code, 'response': response.text}
                )
                
        except requests.exceptions.RequestException as e:
            self.log_test(
                "Parent Dashboard",
                False,
                f"Connection error: {str(e)}",
                {'error': str(e)}
            )

    def test_parent_children_list(self):
        """Test parent children list endpoint"""
        print("👶 Testing Parent Children List Endpoint...")
        
        try:
            response = self.session.get(f"{self.base_url}/mobile/parent/children", timeout=10)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('data'):
                    students = data['data'].get('students', [])
                    
                    if (len(students) == 1 and 
                        students[0]['name'] == 'عبدالرحمن أحمد' and
                        students[0]['id'] == 1):
                        self.log_test(
                            "Parent Children List",
                            True,
                            "Children list returns correct Ahmed Abdullah's child",
                            {
                                'students_count': len(students),
                                'student_name': students[0]['name'],
                                'student_id': students[0]['id']
                            }
                        )
                    else:
                        self.log_test(
                            "Parent Children List",
                            False,
                            "Children list returns incorrect data",
                            {'students': students}
                        )
                else:
                    self.log_test(
                        "Parent Children List",
                        False,
                        "Children list failed - Invalid response format",
                        {'response': data}
                    )
            else:
                self.log_test(
                    "Parent Children List",
                    False,
                    f"Children list failed with status {response.status_code}",
                    {'status_code': response.status_code, 'response': response.text}
                )
                
        except requests.exceptions.RequestException as e:
            self.log_test(
                "Parent Children List",
                False,
                f"Connection error: {str(e)}",
                {'error': str(e)}
            )

    def test_child_details(self):
        """Test child details endpoint"""
        print("👦 Testing Child Details Endpoint...")
        
        # Test valid child ID
        try:
            response = self.session.get(f"{self.base_url}/mobile/parent/children/1", timeout=10)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('data'):
                    student = data['data'].get('student', {})
                    
                    if (student.get('name') == 'عبدالرحمن أحمد' and
                        student.get('id') == 1 and
                        student.get('age') == 12):
                        self.log_test(
                            "Child Details (Valid ID)",
                            True,
                            "Child details returns correct data for Abdulrahman Ahmed",
                            {
                                'student_name': student['name'],
                                'student_id': student['id'],
                                'age': student['age'],
                                'education_level': student.get('education_level')
                            }
                        )
                    else:
                        self.log_test(
                            "Child Details (Valid ID)",
                            False,
                            "Child details returns incorrect data",
                            {'student': student}
                        )
                else:
                    self.log_test(
                        "Child Details (Valid ID)",
                        False,
                        "Child details failed - Invalid response format",
                        {'response': data}
                    )
            else:
                self.log_test(
                    "Child Details (Valid ID)",
                    False,
                    f"Child details failed with status {response.status_code}",
                    {'status_code': response.status_code, 'response': response.text}
                )
                
        except requests.exceptions.RequestException as e:
            self.log_test(
                "Child Details (Valid ID)",
                False,
                f"Connection error: {str(e)}",
                {'error': str(e)}
            )

        # Test invalid child ID
        try:
            response = self.session.get(f"{self.base_url}/mobile/parent/children/999", timeout=10)
            
            if response.status_code == 200:
                data = response.json()
                if not data.get('success'):
                    self.log_test(
                        "Child Details (Invalid ID)",
                        True,
                        f"Correctly rejected invalid child ID: {data.get('message', 'No message')}",
                        {'response': data}
                    )
                else:
                    self.log_test(
                        "Child Details (Invalid ID)",
                        False,
                        "Invalid child ID was accepted",
                        {'response': data}
                    )
            else:
                self.log_test(
                    "Child Details (Invalid ID)",
                    False,
                    f"Unexpected status code {response.status_code}",
                    {'status_code': response.status_code}
                )
                
        except requests.exceptions.RequestException as e:
            self.log_test(
                "Child Details (Invalid ID)",
                False,
                f"Connection error: {str(e)}",
                {'error': str(e)}
            )

    def test_data_consistency(self):
        """Test data consistency across all endpoints"""
        print("🔄 Testing Data Consistency Across Endpoints...")
        
        # Get data from all endpoints and verify consistency
        endpoints_data = {}
        
        # Get dashboard data
        try:
            response = self.session.get(f"{self.base_url}/mobile/parent/dashboard", timeout=10)
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    endpoints_data['dashboard'] = data['data']['children'][0] if data['data']['children'] else None
        except:
            pass
        
        # Get children list data
        try:
            response = self.session.get(f"{self.base_url}/mobile/parent/children", timeout=10)
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    endpoints_data['children'] = data['data']['students'][0] if data['data']['students'] else None
        except:
            pass
        
        # Get child details data
        try:
            response = self.session.get(f"{self.base_url}/mobile/parent/children/1", timeout=10)
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    endpoints_data['details'] = data['data']['student']
        except:
            pass
        
        # Check consistency
        if len(endpoints_data) >= 2:
            consistent = True
            reference_child = list(endpoints_data.values())[0]
            
            for endpoint, child_data in endpoints_data.items():
                if (child_data['id'] != reference_child['id'] or 
                    child_data['name'] != reference_child['name']):
                    consistent = False
                    break
            
            if consistent:
                self.log_test(
                    "Data Consistency",
                    True,
                    "All endpoints return consistent child data",
                    {
                        'child_id': reference_child['id'],
                        'child_name': reference_child['name'],
                        'endpoints_tested': list(endpoints_data.keys())
                    }
                )
            else:
                self.log_test(
                    "Data Consistency",
                    False,
                    "Endpoints return inconsistent child data",
                    {'endpoints_data': endpoints_data}
                )
        else:
            self.log_test(
                "Data Consistency",
                False,
                "Could not retrieve data from enough endpoints for consistency check",
                {'endpoints_data': endpoints_data}
            )

    def run_all_tests(self):
        """Run all tests and generate summary"""
        print("🚀 Starting FastAPI Laravel Proxy Endpoints Tests")
        print("=" * 80)
        print()
        
        # Run all test methods
        self.test_basic_api_connectivity()
        self.test_ahmed_abdullah_login()
        self.test_mohammed_hassan_login()
        self.test_invalid_login_credentials()
        self.test_parent_dashboard()
        self.test_parent_children_list()
        self.test_child_details()
        self.test_data_consistency()
        
        # Generate summary
        print("=" * 80)
        print("📊 TEST SUMMARY")
        print("=" * 80)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results if result['success'])
        failed_tests = total_tests - passed_tests
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {passed_tests} ✅")
        print(f"Failed: {failed_tests} ❌")
        print(f"Success Rate: {(passed_tests/total_tests)*100:.1f}%")
        print()
        
        if failed_tests > 0:
            print("❌ FAILED TESTS:")
            for result in self.test_results:
                if not result['success']:
                    print(f"  - {result['test']}: {result['message']}")
            print()
        
        # Key findings
        print("🔍 KEY FINDINGS:")
        
        # Check login functionality
        login_tests = [r for r in self.test_results if 'Login' in r['test'] and 'Invalid' not in r['test']]
        login_success = all(r['success'] for r in login_tests)
        
        if login_success:
            print("  ✅ FastAPI Laravel proxy login endpoints are working correctly")
        else:
            print("  ❌ FastAPI Laravel proxy login endpoints have issues")
        
        # Check data endpoints
        data_tests = [r for r in self.test_results if any(x in r['test'] for x in ['Dashboard', 'Children', 'Child Details'])]
        data_success = all(r['success'] for r in data_tests)
        
        if data_success:
            print("  ✅ FastAPI Laravel proxy data endpoints are working correctly")
        else:
            print("  ❌ FastAPI Laravel proxy data endpoints have issues")
        
        # Check security
        security_tests = [r for r in self.test_results if 'Invalid' in r['test']]
        security_success = all(r['success'] for r in security_tests)
        
        if security_success:
            print("  ✅ Security validation is working correctly")
        else:
            print("  ❌ Security validation needs attention")
        
        print()
        print("🎯 CONCLUSION:")
        if passed_tests == total_tests:
            print("  ✅ ALL FastAPI Laravel Proxy Endpoints are working correctly!")
            print("  ✅ The routes are now properly loaded and accessible")
        else:
            print(f"  ⚠️  {failed_tests} out of {total_tests} tests failed - Some endpoints need attention")
        
        return passed_tests == total_tests

def main():
    """Main test execution"""
    tester = FastAPILaravelProxyTester()
    success = tester.run_all_tests()
    
    # Exit with appropriate code
    sys.exit(0 if success else 1)

if __name__ == "__main__":
    main()