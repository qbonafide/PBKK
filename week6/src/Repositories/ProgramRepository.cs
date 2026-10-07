using Microsoft.Data.SqlClient;
using StudentRegistration.Models;

namespace StudentRegistration.Repositories;

public class ProgramRepository
{
    public List<Program> GetActive()
    {
        var programs = new List<Program>();

        const string sql = @"
            SELECT ProgramId, ProgramCode, ProgramName, IsActive
            FROM Programs
            WHERE IsActive = 1
            ORDER BY ProgramName;";

        using var connection = new SqlConnection(DbConfig.ConnectionString);
        using var command = new SqlCommand(sql, connection);
        connection.Open();

        using var reader = command.ExecuteReader();
        while (reader.Read())
        {
            programs.Add(new Program
            {
                ProgramId = reader.GetInt32(reader.GetOrdinal("ProgramId")),
                ProgramCode = reader.GetString(reader.GetOrdinal("ProgramCode")),
                ProgramName = reader.GetString(reader.GetOrdinal("ProgramName")),
                IsActive = reader.GetBoolean(reader.GetOrdinal("IsActive"))
            });
        }

        return programs;
    }
}